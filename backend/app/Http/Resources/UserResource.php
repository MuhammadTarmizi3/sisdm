<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $role = $this->roles->first();

        return [
            'USER_ID' => $this->USER_ID,
            'USERNAME' => $this->USERNAME,
            'IS_ACTIVE' => (bool) $this->IS_ACTIVE,
            'role' => $role ? [
                'KODE_ROLE' => $role->name,
                'NAMA_ROLE' => collect(explode('_', $role->name))->map('ucfirst')->join(' '),
            ] : null,
            'pegawai_id' => $this->ID_PEGAWAI,
        ];
    }
}
