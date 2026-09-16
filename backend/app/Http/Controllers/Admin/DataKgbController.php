<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailKgb;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class DataKgbController extends Controller
{
    public function show($pegawaiId)
    {
        $pengajuan = Pengajuan::where('ID_PEGAWAI', $pegawaiId)
            ->where('STATUS_PENGAJUAN', 'Disetujui')
            ->latest()
            ->firstOrFail();

        $detailKgb = DetailKgb::where('ID_PENGAJUAN', $pengajuan->ID_PENGAJUAN)->first();
        
        return response()->json(['data' => $detailKgb]);
    }

    public function update(Request $request, $pegawaiId)
    {
        $pengajuan = Pengajuan::where('ID_PEGAWAI', $pegawaiId)
            ->where('STATUS_PENGAJUAN', 'Disetujui')
            ->latest()
            ->firstOrFail();

        $detailKgb = DetailKgb::where('ID_PENGAJUAN', $pengajuan->ID_PENGAJUAN)->firstOrFail();
        $detailKgb->update($request->all());

        return response()->json(['message' => 'Data KGB berhasil diupdate', 'data' => $detailKgb]);
    }
}
