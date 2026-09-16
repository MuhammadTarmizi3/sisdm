<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\Pengajuan;
use App\Models\DetailKgb;
use App\Models\PersyaratanMaster;
use App\Models\DetailBerkas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PengajuanService
{
    protected AuditService $auditService;
    protected NotifikasiService $notifikasiService;

    public function __construct(AuditService $auditService, NotifikasiService $notifikasiService)
    {
        $this->auditService = $auditService;
        $this->notifikasiService = $notifikasiService;
    }

    public function buatPengajuan(Pegawai $pegawai, array $data): Pengajuan
    {
        $activeExists = Pengajuan::where('ID_PEGAWAI', $pegawai->ID_PEGAWAI)
            ->whereIn('STATUS_PENGAJUAN', ['Diajukan', 'Perlu perbaikan'])
            ->exists();

        if ($activeExists) {
            throw new HttpException(409, 'Terdapat pengajuan aktif untuk pegawai ini.');
        }

        return DB::transaction(function () use ($pegawai, $data) {
            $pengajuan = Pengajuan::create([
                'ID_PEGAWAI' => $pegawai->ID_PEGAWAI,
                'ID_LAYANAN' => $data['ID_LAYANAN'] ?? null,
                'STATUS_PENGAJUAN' => 'Diajukan',
                'TANGGAL_PENGAJUAN' => Carbon::now(),
            ]);

            DetailKgb::create([
                'ID_PENGAJUAN' => $pengajuan->ID_PENGAJUAN,
                'GAJI_POKOK_LAMA' => $data['gaji_pokok_lama'] ?? null,
                'GAJI_POKOK_BARU' => $data['gaji_pokok_baru'] ?? null,
                'TMT_KGB_BERIKUTNYA' => $data['tmt_kgb_berikutnya'] ?? null,
            ]);

            $this->notifikasiService->kirimKeRole('verifikator', 'Pengajuan Baru', 'Ada pengajuan baru', 'pengajuan_baru');
            
            if ($pegawai->user) {
                $this->auditService->log($pegawai->user->USER_ID, 'Membuat pengajuan baru', 'Pengajuan', $pengajuan->ID_PENGAJUAN);
            }

            return $pengajuan;
        });
    }

    public function kirimUlangPerbaikan(Pengajuan $pengajuan): Pengajuan
    {
        if ($pengajuan->STATUS_PENGAJUAN !== 'Perlu perbaikan') {
            throw new HttpException(409, 'Status pengajuan bukan Perlu perbaikan.');
        }

        $wajibPersyaratan = PersyaratanMaster::where('ID_LAYANAN', $pengajuan->ID_LAYANAN)
            ->where('WAJIB', true)
            ->where('IS_ACTIVE', true)
            ->get();

        $uploadedPersyaratanIds = DetailBerkas::where('ID_PENGAJUAN', $pengajuan->ID_PENGAJUAN)
            ->whereNotNull('FILE_PATH')
            ->pluck('ID_PERSYARATAN')
            ->toArray();

        foreach ($wajibPersyaratan as $persyaratan) {
            if (!in_array($persyaratan->ID_PERSYARATAN, $uploadedPersyaratanIds)) {
                throw new HttpException(409, 'Masih terdapat berkas wajib yang belum diunggah.');
            }
        }

        $pengajuan->STATUS_PENGAJUAN = 'Diajukan';
        $pengajuan->save();

        $this->notifikasiService->kirimKeRole('verifikator', 'Perbaikan Pengajuan', 'Pengajuan telah diperbaiki', 'perbaikan_pengajuan');
        
        $userId = auth()->id() ?? 1;
        $this->auditService->log($userId, 'Mengirim ulang perbaikan pengajuan', 'Pengajuan', $pengajuan->ID_PENGAJUAN);

        return $pengajuan;
    }

    public function inputSk(Pengajuan $pengajuan, string $nomerSk, string $tanggalSk): Pengajuan
    {
        if ($pengajuan->STATUS_PENGAJUAN !== 'Disetujui') {
            throw new HttpException(409, 'Status pengajuan belum disetujui.');
        }

        $tglSk = Carbon::parse($tanggalSk);
        $tglPengajuan = Carbon::parse($pengajuan->TANGGAL_PENGAJUAN);

        if ($tglSk->lessThan($tglPengajuan->startOfDay())) {
            throw new HttpException(409, 'Tanggal SK tidak boleh sebelum tanggal pengajuan.');
        }

        return DB::transaction(function () use ($pengajuan, $nomerSk, $tanggalSk) {
            $pengajuan->NOMER_SK = $nomerSk;
            $pengajuan->TANGGAL_SK = $tanggalSk;
            $pengajuan->save();

            $userId = auth()->id() ?? 1;
            $this->auditService->log($userId, 'Input SK pengajuan', 'Pengajuan', $pengajuan->ID_PENGAJUAN);

            return $pengajuan;
        });
    }
}
