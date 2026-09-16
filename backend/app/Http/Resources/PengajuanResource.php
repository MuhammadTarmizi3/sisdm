<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengajuanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_PENGAJUAN' => $this->ID_PENGAJUAN,
            'ID_PEGAWAI' => $this->ID_PEGAWAI,
            'pegawai' => $this->pegawai ? $this->pegawai->NAMA_PEGAWAI : null,
            'ID_LAYANAN' => $this->ID_LAYANAN,
            'TANGGAL_PENGAJUAN' => $this->TANGGAL_PENGAJUAN,
            'STATUS_PENGAJUAN' => $this->STATUS_PENGAJUAN,
            'CATATAN_VERIFIKATOR' => $this->CATATAN_VERIFIKATOR,
            'NOMER_SK' => $this->NOMER_SK,
            'TANGGAL_SK' => $this->TANGGAL_SK,
            'detail_berkas' => DetailBerkasResource::collection($this->whenLoaded('detailBerkas')),
            'detail_kgb' => $this->whenLoaded('detailKgb'),
            'status' => $this->STATUS_PENGAJUAN,
        ];
    }
}
