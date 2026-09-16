<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Http\Resources\PegawaiResource;
use App\Models\PersyaratanMaster;
use App\Services\KgbCalculatorService;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function show()
    {
        $pegawai = Auth::user()->pegawai()->with(['riwayatJabatan', 'riwayatPangkat'])->first();

        return response()->json([
            'data' => new PegawaiResource($pegawai),
            'riwayat_jabatan' => $pegawai->riwayatJabatan,
            'riwayat_pangkat' => $pegawai->riwayatPangkat,
        ]);
    }

    public function pengingatKgb(KgbCalculatorService $kgbService)
    {
        $pegawai = Auth::user()->pegawai;
        $pengingat = $kgbService->cekPengingat($pegawai);

        return response()->json(['data' => $pengingat]);
    }

    public function persyaratanKgb()
    {
        $persyaratan = PersyaratanMaster::where('IS_ACTIVE', true)
            ->whereHas('jenisLayanan', function($q) {
                $q->where('NAMA_LAYANAN', 'KGB');
            })
            ->orderBy('URUTAN')
            ->get();

        return response()->json(['data' => $persyaratan]);
    }
}
