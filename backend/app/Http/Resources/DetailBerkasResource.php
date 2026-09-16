<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailBerkasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_DETAIL_BERKAS' => $this->ID_DETAIL_BERKAS,
            'ID_PENGAJUAN' => $this->ID_PENGAJUAN,
            'ID_PERSYARATAN' => $this->ID_PERSYARATAN,
            'persyaratan' => $this->persyaratanMaster ? $this->persyaratanMaster->NAMA_PERSYARATAN : null,
            'wajib' => $this->persyaratanMaster ? (bool) $this->persyaratanMaster->WAJIB : null,
            'FILE_PATH' => $this->FILE_PATH,
            'STATUS_VERIFIKASI' => $this->STATUS_VERIFIKASI,
            'CATATAN' => $this->CATATAN,
        ];
    }
}
