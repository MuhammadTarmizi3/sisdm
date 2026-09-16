<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotifikasiResource;
use App\Services\NotifikasiService;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    protected $notifikasiService;

    public function __construct(NotifikasiService $notifikasiService)
    {
        $this->notifikasiService = $notifikasiService;
    }

    public function index()
    {
        $userId = Auth::id();
        $notifikasi = $this->notifikasiService->getUnread($userId);

        return response()->json(['data' => NotifikasiResource::collection($notifikasi)]);
    }

    public function tandaiDibaca($id)
    {
        $this->notifikasiService->tandaiDibaca($id);

        return response()->json(['message' => 'Notifikasi ditandai dibaca']);
    }
}
