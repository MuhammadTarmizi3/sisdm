<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Pegawai\ProfilController;
use App\Http\Controllers\Pegawai\PengajuanKgbController;
use App\Http\Controllers\Pegawai\NotifikasiController;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\UnitKerjaController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\PangkatGolonganController;
use App\Http\Controllers\Admin\RiwayatController;
use App\Http\Controllers\Admin\DataKgbController;
use App\Http\Controllers\Admin\PersyaratanKgbController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EksporController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Admin\SkController;
use App\Http\Controllers\Verifikator\PengajuanMasukController;
use App\Http\Controllers\Verifikator\MemenuhiSyaratController;

Route::prefix('v1')->group(function () {
    // Auth (public)
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        
        // Pegawai routes
        Route::prefix('pegawai')->middleware('role:pegawai')->group(function () {
            Route::get('/profil', [ProfilController::class, 'show']);
            Route::get('/pengingat-kgb', [ProfilController::class, 'pengingatKgb']);
            Route::get('/persyaratan-kgb', [ProfilController::class, 'persyaratanKgb']);
            
            Route::get('/pengajuan-kgb', [PengajuanKgbController::class, 'index']);
            Route::post('/pengajuan-kgb', [PengajuanKgbController::class, 'store']);
            Route::get('/pengajuan-kgb/{id}', [PengajuanKgbController::class, 'show']);
            Route::post('/pengajuan-kgb/{id}/upload', [PengajuanKgbController::class, 'uploadBerkas']);
            Route::post('/pengajuan-kgb/{id}/perbaikan', [PengajuanKgbController::class, 'perbaikan']);
            
            Route::get('/notifikasi', [NotifikasiController::class, 'index']);
            Route::post('/notifikasi/{id}/tandai-dibaca', [NotifikasiController::class, 'tandaiDibaca']);
        });
        
        // Admin routes  
        Route::prefix('admin')->middleware('role:admin_kepegawaian')->group(function () {
            Route::get('/akun', [AkunController::class, 'index']);
            Route::post('/akun', [AkunController::class, 'store']);
            Route::get('/akun/{akun}', [AkunController::class, 'show']);
            Route::put('/akun/{akun}', [AkunController::class, 'update']);
            Route::delete('/akun/{akun}', [AkunController::class, 'destroy']);
            
            Route::get('/pegawai', [PegawaiController::class, 'index']);
            Route::post('/pegawai', [PegawaiController::class, 'store']);
            Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show']);
            Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update']);
            Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy']);
            
            Route::get('/unit-kerja', [UnitKerjaController::class, 'index']);
            Route::post('/unit-kerja', [UnitKerjaController::class, 'store']);
            Route::get('/unit-kerja/{id}', [UnitKerjaController::class, 'show']);
            Route::put('/unit-kerja/{id}', [UnitKerjaController::class, 'update']);
            Route::delete('/unit-kerja/{id}', [UnitKerjaController::class, 'destroy']);
            
            Route::get('/jabatan', [JabatanController::class, 'index']);
            Route::post('/jabatan', [JabatanController::class, 'store']);
            Route::get('/jabatan/{id}', [JabatanController::class, 'show']);
            Route::put('/jabatan/{id}', [JabatanController::class, 'update']);
            Route::delete('/jabatan/{id}', [JabatanController::class, 'destroy']);
            
            Route::get('/pangkat-golongan', [PangkatGolonganController::class, 'index']);
            Route::post('/pangkat-golongan', [PangkatGolonganController::class, 'store']);
            Route::get('/pangkat-golongan/{id}', [PangkatGolonganController::class, 'show']);
            Route::put('/pangkat-golongan/{id}', [PangkatGolonganController::class, 'update']);
            Route::delete('/pangkat-golongan/{id}', [PangkatGolonganController::class, 'destroy']);
            
            Route::get('/pegawai/{id}/riwayat-jabatan', [RiwayatController::class, 'indexJabatan']);
            Route::post('/pegawai/{id}/riwayat-jabatan', [RiwayatController::class, 'storeJabatan']);
            Route::get('/pegawai/{id}/riwayat-pangkat', [RiwayatController::class, 'indexPangkat']);
            Route::post('/pegawai/{id}/riwayat-pangkat', [RiwayatController::class, 'storePangkat']);
            
            Route::get('/pegawai/{id}/data-kgb', [DataKgbController::class, 'show']);
            Route::put('/pegawai/{id}/data-kgb', [DataKgbController::class, 'update']);
            
            Route::get('/persyaratan-kgb', [PersyaratanKgbController::class, 'index']);
            Route::post('/persyaratan-kgb', [PersyaratanKgbController::class, 'store']);
            Route::get('/persyaratan-kgb/{id}', [PersyaratanKgbController::class, 'show']);
            Route::put('/persyaratan-kgb/{id}', [PersyaratanKgbController::class, 'update']);
            Route::delete('/persyaratan-kgb/{id}', [PersyaratanKgbController::class, 'destroy']);
            
            Route::get('/dashboard', [DashboardController::class, 'index']);
            Route::get('/ekspor/rekap-excel', [EksporController::class, 'rekapExcel']);
            Route::get('/ekspor/rekap-pdf', [EksporController::class, 'rekapPdf']);
            Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);
            
            Route::post('/pengajuan/{id}/sk', [SkController::class, 'store']);
        });
        
        // Verifikator routes
        Route::prefix('verifikator')->middleware('role:verifikator')->group(function () {
            Route::get('/pengajuan-masuk', [PengajuanMasukController::class, 'index']);
            Route::get('/pengajuan-masuk/{id}', [PengajuanMasukController::class, 'show']);
            Route::post('/pengajuan-masuk/{id}/berkas/{berkasId}/verifikasi', [PengajuanMasukController::class, 'verifikasiBerkas']);
            Route::post('/pengajuan-masuk/{id}/setujui', [PengajuanMasukController::class, 'setujui']);
            Route::post('/pengajuan-masuk/{id}/tolak', [PengajuanMasukController::class, 'tolak']);
            Route::post('/pengajuan-masuk/{id}/kembalikan', [PengajuanMasukController::class, 'kembalikan']);
            
            Route::get('/memenuhi-syarat', [MemenuhiSyaratController::class, 'index']);
            Route::get('/memenuhi-syarat/ekspor', [MemenuhiSyaratController::class, 'ekspor']);
        });
    });
});
