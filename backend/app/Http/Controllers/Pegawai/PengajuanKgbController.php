<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pegawai\StorePengajuanKgbRequest;
use App\Http\Requests\Pegawai\UploadBerkasRequest;
use App\Http\Resources\PengajuanResource;
use App\Models\Pengajuan;
use App\Services\PengajuanService;
use Illuminate\Support\Facades\Auth;

class PengajuanKgbController extends Controller
{
    protected $pengajuanService;

    public function __construct(PengajuanService $pengajuanService)
    {
        $this->pengajuanService = $pengajuanService;
    }

    public function index()
    {
        $pegawaiId = Auth::user()->pegawai->ID_PEGAWAI;
        $pengajuan = Pengajuan::where('ID_PEGAWAI', $pegawaiId)->with('detailBerkas', 'detailKgb')->get();

        return response()->json(['data' => PengajuanResource::collection($pengajuan)]);
    }

    public function store(StorePengajuanKgbRequest $request)
    {
        $pegawai = Auth::user()->pegawai;
        $data = $request->validated();
        $pengajuan = $this->pengajuanService->buatPengajuan($pegawai, $data);

        return response()->json([
            'message' => 'Pengajuan KGB berhasil dibuat',
            'data' => new PengajuanResource($pengajuan)
        ], 201);
    }

    public function show($id)
    {
        $pengajuan = Pengajuan::with('detailBerkas', 'detailKgb')->findOrFail($id);
        $this->authorize('view', $pengajuan);

        return response()->json(['data' => new PengajuanResource($pengajuan)]);
    }

    public function uploadBerkas(UploadBerkasRequest $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->authorize('update', $pengajuan);

        $file = $request->file('file');
        $path = $file->store('berkas');

        $pengajuan->detailBerkas()->updateOrCreate(
            ['ID_PERSYARATAN' => $request->ID_PERSYARATAN],
            ['FILE_PATH' => $path, 'STATUS_VERIFIKASI' => 'Belum Diverifikasi']
        );

        return response()->json(['message' => 'Berkas berhasil diupload']);
    }

    public function perbaikan($id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->authorize('update', $pengajuan);

        $this->pengajuanService->kirimUlangPerbaikan($pengajuan);

        return response()->json(['message' => 'Pengajuan berhasil dikirim ulang']);
    }
}
