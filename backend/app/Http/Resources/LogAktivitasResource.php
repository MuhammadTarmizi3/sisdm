<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogAktivitasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_LOG' => $this->ID_LOG,
            'USER_ID' => $this->USER_ID,
            'user' => $this->user ? $this->user->USERNAME : null,
            'AKTIVITAS' => $this->AKTIVITAS,
            'ENTITY_TYPE' => $this->ENTITY_TYPE,
            'ENTITY_ID' => $this->ENTITY_ID,
            'WAKTU' => $this->WAKTU,
            'IP_ADDRESS' => $this->IP_ADDRESS,
        ];
    }
}
