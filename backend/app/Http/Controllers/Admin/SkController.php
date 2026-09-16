<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InputSkRequest;
use App\Models\Pengajuan;
use App\Services\PengajuanService;

class SkController extends Controller
{
    protected $pengajuanService;

    public function __construct(PengajuanService $pengajuanService)
    {
        $this->pengajuanService = $pengajuanService;
    }

    public function store(InputSkRequest $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $this->pengajuanService->inputSk($pengajuan, $request->NOMER_SK, $request->TANGGAL_SK);

        return response()->json(['message' => 'SK berhasil diinput']);
    }
}
