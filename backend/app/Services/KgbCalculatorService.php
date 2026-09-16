<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class KgbCalculatorService
{
    public function hitungTmtBerikutnya(Carbon $tmtKgbTerakhir, int $intervalTahun = 2): Carbon
    {
        return $tmtKgbTerakhir->copy()->addYears($intervalTahun);
    }

    public function cekPengingat(Pegawai $pegawai): ?array
    {
        $pengajuan = Pengajuan::where('ID_PEGAWAI', $pegawai->ID_PEGAWAI)
            ->where('STATUS_PENGAJUAN', 'Disetujui')
            ->with('detailKgb')
            ->latest('TANGGAL_PENGAJUAN')
            ->first();

        if (!$pengajuan || !$pengajuan->detailKgb || !$pengajuan->detailKgb->TMT_KGB_BERIKUTNYA) {
            return null;
        }

        $tmtBerikutnya = Carbon::parse($pengajuan->detailKgb->TMT_KGB_BERIKUTNYA);
        $sekarang = Carbon::now();
        
        $diffDays = $sekarang->diffInDays($tmtBerikutnya, false);

        if ($diffDays >= 0 && $diffDays <= 90) {
            return [
                'tmt_kgb_berikutnya' => $tmtBerikutnya->toDateString(),
                'hari_tersisa' => (int) $diffDays
            ];
        }

        return null;
    }

    public function deteksiSemuaPengingat(): Collection
    {
        $pegawais = Pegawai::all();
        $result = collect();

        foreach ($pegawais as $pegawai) {
            $pengingat = $this->cekPengingat($pegawai);
            if ($pengingat !== null) {
                $result->push([
                    'pegawai' => $pegawai,
                    'pengingat' => $pengingat
                ]);
            }
        }

        return $result;
    }
}
