<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_pengajuan' => Pengajuan::count(),
            'disetujui' => Pengajuan::where('STATUS_PENGAJUAN', 'Disetujui')->count(),
            'ditolak' => Pengajuan::where('STATUS_PENGAJUAN', 'Ditolak')->count(),
            'diajukan' => Pengajuan::where('STATUS_PENGAJUAN', 'Diajukan')->count(),
        ];

        return response()->json(['data' => $stats]);
    }
}
