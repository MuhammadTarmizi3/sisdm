<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Http\Resources\PengajuanResource;
use App\Models\Pengajuan;
use App\Services\EksporService;

class MemenuhiSyaratController extends Controller
{
    protected $eksporService;

    public function __construct(EksporService $eksporService)
    {
        $this->eksporService = $eksporService;
    }

    public function index()
    {
        $pengajuan = Pengajuan::where('STATUS_PENGAJUAN', 'Disetujui')->with('pegawai')->get();
        return response()->json(['data' => PengajuanResource::collection($pengajuan)]);
    }

    public function ekspor()
    {
        return $this->eksporService->eksporMemenuhiSyaratExcel();
    }
}
