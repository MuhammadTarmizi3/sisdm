<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiwayatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_RIWAYAT' => $this->ID_RIWAYAT_JABATAN ?? $this->ID_RIWAYAT_PANGKAT,
            'ID_PEGAWAI' => $this->ID_PEGAWAI,
            'NAMA' => $this->NAMA_JABATAN ?? $this->NAMA_PANGKAT,
            'TMT' => $this->TMT_JABATAN ?? $this->TMT_PANGKAT,
            'NOMER_SK' => $this->NOMER_SK,
            'TANGGAL_SK' => $this->TANGGAL_SK,
        ];
    }
}
