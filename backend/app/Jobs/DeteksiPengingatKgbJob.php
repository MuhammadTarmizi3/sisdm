<?php

namespace App\Jobs;

use App\Models\Pegawai;
use App\Models\Notifikasi;
use App\Models\DetailKgb;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeteksiPengingatKgbJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $tigaBulanDariSekarang = Carbon::now()->addMonths(3);

        // Get all active pegawai who have approved KGB pengajuan with TMT data
        $pegawaiList = Pegawai::whereNull('deleted_at')
            ->whereHas('user')
            ->get();

        foreach ($pegawaiList as $pegawai) {
            // Get latest approved pengajuan's detail_kgb
            $latestKgb = DetailKgb::whereHas('pengajuan', function ($q) use ($pegawai) {
                $q->where('ID_PEGAWAI', $pegawai->ID_PEGAWAI)
                  ->where('STATUS_PENGAJUAN', 'Disetujui');
            })
            ->orderByDesc('TMT_KGB_BERIKUTNYA')
            ->first();

            if (!$latestKgb || !$latestKgb->TMT_KGB_BERIKUTNYA) {
                continue;
            }

            $tmtBerikutnya = Carbon::parse($latestKgb->TMT_KGB_BERIKUTNYA);
            $ambangPengingat = $tmtBerikutnya->copy()->subMonths(3);

            // Check if within reminder window
            if (Carbon::now()->gte($ambangPengingat) && Carbon::now()->lte($tmtBerikutnya)) {
                // Check if active pengajuan exists
                $activePengajuan = Pengajuan::where('ID_PEGAWAI', $pegawai->ID_PEGAWAI)
                    ->whereIn('STATUS_PENGAJUAN', ['Diajukan', 'Perlu perbaikan'])
                    ->exists();

                if ($activePengajuan) {
                    continue;
                }

                // Check if reminder already sent for this cycle
                $reminderExists = Notifikasi::where('USER_ID', $pegawai->user->USER_ID)
                    ->where('JENIS', 'pengingat_kgb')
                    ->where('CREATED_AT', '>=', $ambangPengingat)
                    ->exists();

                if ($reminderExists) {
                    continue;
                }

                // Send reminder
                $hariTersisa = Carbon::now()->diffInDays($tmtBerikutnya);
                Notifikasi::create([
                    'USER_ID' => $pegawai->user->USER_ID,
                    'JUDUL' => 'Pengingat KGB',
                    'PESAN' => "KGB Anda akan jatuh tempo pada {$tmtBerikutnya->format('d-m-Y')} ({$hariTersisa} hari lagi). Silakan segera mengajukan KGB.",
                    'JENIS' => 'pengingat_kgb',
                    'IS_READ' => false,
                    'CREATED_AT' => now(),
                ]);
            }
        }
    }
}
