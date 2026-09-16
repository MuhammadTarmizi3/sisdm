<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PegawaiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ID_PEGAWAI' => $this->ID_PEGAWAI,
            'NIP_NRP' => $this->NIP_NRP,
            'NAMA_PEGAWAI' => $this->NAMA_PEGAWAI,
            'TEMPAT_LAHIR' => $this->TEMPAT_LAHIR,
            'TGL_LAHIR' => $this->TGL_LAHIR,
            'JENIS_KELAMIN' => $this->JENIS_KELAMIN,
            'NOMER_HP' => $this->NOMER_HP,
            'STATUS_KEPEGAWAIAN' => $this->STATUS_KEPEGAWAIAN,
            'TMT_CPNS' => $this->TMT_CPNS,
            'TMT_PNS' => $this->TMT_PNS,
            'PENDIDIKAN_TERAKHIR' => $this->PENDIDIKAN_TERAKHIR,
            'FOTO' => $this->FOTO,
            'ID_UNIT' => $this->ID_UNIT,
            'unit_kerja' => $this->unitKerja ? $this->unitKerja->NAMA_UNIT : null,
            'ID_JABATAN' => $this->ID_JABATAN,
            'jabatan' => $this->jabatan ? $this->jabatan->NAMA_JABATAN : null,
            'ID_PANGKAT' => $this->ID_PANGKAT,
            'pangkat' => $this->pangkatGolongan ? $this->pangkatGolongan->NAMA_PANGKAT : null,
        ];
    }
}
