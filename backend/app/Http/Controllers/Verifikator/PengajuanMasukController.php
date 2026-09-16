<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Verifikator\KeputusanRequest;
use App\Http\Requests\Verifikator\VerifikasiBerkasRequest;
use App\Http\Resources\PengajuanResource;
use App\Models\Pengajuan;
use App\Models\DetailBerkas;
use App\Services\VerifikasiService;

class PengajuanMasukController extends Controller
{
    protected $verifikasiService;

    public function __construct(VerifikasiService $verifikasiService)
    {
        $this->verifikasiService = $verifikasiService;
    }

    public function index()
    {
        $pengajuan = Pengajuan::where('STATUS_PENGAJUAN', 'Diajukan')->with('pegawai', 'detailBerkas')->get();
        return response()->json(['data' => PengajuanResource::collection($pengajuan)]);
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::with('pegawai', 'detailBerkas')->findOrFail($id);
        return response()->json(['data' => new PengajuanResource($pengajuan)]);
    }

    public function verifikasiBerkas(VerifikasiBerkasRequest $request, $pengajuanId, $berkasId)
    {
        $berkas = DetailBerkas::where('ID_PENGAJUAN', $pengajuanId)->findOrFail($berkasId);
        $this->verifikasiService->setStatusBerkas($berkas, $request->STATUS_VERIFIKASI, $request->CATATAN);

        return response()->json(['message' => 'Status berkas berhasil diupdate']);
    }

    public function setujui($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->verifikasiService->setujui($pengajuan);

        return response()->json(['message' => 'Pengajuan disetujui']);
    }

    public function tolak(KeputusanRequest $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->verifikasiService->tolak($pengajuan, $request->CATATAN);

        return response()->json(['message' => 'Pengajuan ditolak']);
    }

    public function kembalikan(KeputusanRequest $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->verifikasiService->kembalikan($pengajuan, $request->CATATAN);

        return response()->json(['message' => 'Pengajuan dikembalikan untuk perbaikan']);
    }
}
