<?php

namespace App\Services;

use App\Models\Pengajuan;
use App\Models\DetailBerkas;
use App\Models\ApprovalLog;
use App\Models\TahapanApproval;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Carbon\Carbon;

class VerifikasiService
{
    protected AuditService $auditService;
    protected NotifikasiService $notifikasiService;

    public function __construct(AuditService $auditService, NotifikasiService $notifikasiService)
    {
        $this->auditService = $auditService;
        $this->notifikasiService = $notifikasiService;
    }

    public function setStatusBerkas(DetailBerkas $berkas, string $status, ?string $catatan = null): DetailBerkas
    {
        $berkas->STATUS_VERIFIKASI = $status;
        $berkas->CATATAN = $catatan;
        $berkas->save();

        return $berkas;
    }

    public function setujui(Pengajuan $pengajuan, int $userId): Pengajuan
    {
        $wajibInvalid = DetailBerkas::where('ID_PENGAJUAN', $pengajuan->ID_PENGAJUAN)
            ->whereHas('persyaratanMaster', function ($query) {
                $query->where('WAJIB', true);
            })
            ->where(function ($query) {
                $query->where('STATUS_VERIFIKASI', '!=', 'valid')
                      ->orWhereNull('STATUS_VERIFIKASI');
            })
            ->exists();

        if ($wajibInvalid) {
            throw new HttpException(409, 'Masih terdapat berkas wajib yang belum valid');
        }

        return DB::transaction(function () use ($pengajuan, $userId) {
            $pengajuan->STATUS_PENGAJUAN = 'Disetujui';
            $pengajuan->save();

            $tahapan = TahapanApproval::where('ID_LAYANAN', $pengajuan->ID_LAYANAN)->first();

            ApprovalLog::create([
                'ID_PENGAJUAN' => $pengajuan->ID_PENGAJUAN,
                'ID_TAHAPAN' => $tahapan ? $tahapan->ID_APPROVAL : null,
                'ID_USER' => $userId,
                'KEPUTUSAN' => 'Disetujui',
                'CATATAN' => null,
                'WAKTU' => Carbon::now(),
            ]);

            if ($pengajuan->pegawai && $pengajuan->pegawai->user) {
                $this->notifikasiService->kirim(
                    $pengajuan->pegawai->user->USER_ID,
                    'Pengajuan Disetujui',
                    'Pengajuan Anda telah disetujui.',
                    'disetujui'
                );
            }

            $this->auditService->log($userId, 'Menyetujui pengajuan', 'Pengajuan', $pengajuan->ID_PENGAJUAN);

            return $pengajuan;
        });
    }

    public function tolak(Pengajuan $pengajuan, int $userId, string $alasan): Pengajuan
    {
        return DB::transaction(function () use ($pengajuan, $userId, $alasan) {
            $pengajuan->STATUS_PENGAJUAN = 'Ditolak';
            $pengajuan->CATATAN_VERIFIKATOR = $alasan;
            $pengajuan->save();

            $tahapan = TahapanApproval::where('ID_LAYANAN', $pengajuan->ID_LAYANAN)->first();

            ApprovalLog::create([
                'ID_PENGAJUAN' => $pengajuan->ID_PENGAJUAN,
                'ID_TAHAPAN' => $tahapan ? $tahapan->ID_APPROVAL : null,
                'ID_USER' => $userId,
                'KEPUTUSAN' => 'Ditolak',
                'CATATAN' => $alasan,
                'WAKTU' => Carbon::now(),
            ]);

            if ($pengajuan->pegawai && $pengajuan->pegawai->user) {
                $this->notifikasiService->kirim(
                    $pengajuan->pegawai->user->USER_ID,
                    'Pengajuan Ditolak',
                    'Pengajuan Anda ditolak. Alasan: ' . $alasan,
                    'ditolak'
                );
            }

            $this->auditService->log($userId, 'Menolak pengajuan', 'Pengajuan', $pengajuan->ID_PENGAJUAN);

            return $pengajuan;
        });
    }

    public function kembalikan(Pengajuan $pengajuan, int $userId, string $catatan): Pengajuan
    {
        return DB::transaction(function () use ($pengajuan, $userId, $catatan) {
            $pengajuan->STATUS_PENGAJUAN = 'Perlu perbaikan';
            $pengajuan->CATATAN_VERIFIKATOR = $catatan;
            $pengajuan->save();

            $tahapan = TahapanApproval::where('ID_LAYANAN', $pengajuan->ID_LAYANAN)->first();

            ApprovalLog::create([
                'ID_PENGAJUAN' => $pengajuan->ID_PENGAJUAN,
                'ID_TAHAPAN' => $tahapan ? $tahapan->ID_APPROVAL : null,
                'ID_USER' => $userId,
                'KEPUTUSAN' => 'Perlu perbaikan',
                'CATATAN' => $catatan,
                'WAKTU' => Carbon::now(),
            ]);

            if ($pengajuan->pegawai && $pengajuan->pegawai->user) {
                $this->notifikasiService->kirim(
                    $pengajuan->pegawai->user->USER_ID,
                    'Pengajuan Perlu Perbaikan',
                    'Pengajuan Anda perlu perbaikan. Catatan: ' . $catatan,
                    'perlu_perbaikan'
                );
            }

            $this->auditService->log($userId, 'Mengembalikan pengajuan untuk perbaikan', 'Pengajuan', $pengajuan->ID_PENGAJUAN);

            return $pengajuan;
        });
    }
}
