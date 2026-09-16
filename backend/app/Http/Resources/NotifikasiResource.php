<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotifikasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_NOTIFIKASI' => $this->ID_NOTIFIKASI,
            'USER_ID' => $this->USER_ID,
            'JUDUL' => $this->JUDUL,
            'PESAN' => $this->PESAN,
            'TIPE_NOTIFIKASI' => $this->TIPE_NOTIFIKASI,
            'IS_READ' => (bool) $this->IS_READ,
            'CREATED_AT' => $this->CREATED_AT,
        ];
    }
}
