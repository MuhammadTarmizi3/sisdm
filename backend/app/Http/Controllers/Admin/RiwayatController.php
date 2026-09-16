<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatPangkat;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function indexJabatan($pegawaiId)
    {
        $riwayat = RiwayatJabatan::where('ID_PEGAWAI', $pegawaiId)->get();
        return response()->json(['data' => $riwayat]);
    }

    public function storeJabatan(Request $request, $pegawaiId)
    {
        $riwayat = RiwayatJabatan::create(array_merge($request->all(), ['ID_PEGAWAI' => $pegawaiId]));
        return response()->json(['message' => 'Riwayat jabatan berhasil ditambahkan', 'data' => $riwayat], 201);
    }

    public function indexPangkat($pegawaiId)
    {
        $riwayat = RiwayatPangkat::where('ID_PEGAWAI', $pegawaiId)->get();
        return response()->json(['data' => $riwayat]);
    }

    public function storePangkat(Request $request, $pegawaiId)
    {
        $riwayat = RiwayatPangkat::create(array_merge($request->all(), ['ID_PEGAWAI' => $pegawaiId]));
        return response()->json(['message' => 'Riwayat pangkat berhasil ditambahkan', 'data' => $riwayat], 201);
    }
}
