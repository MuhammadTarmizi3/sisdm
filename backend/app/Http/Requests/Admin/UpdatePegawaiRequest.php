<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'NIP_NRP' => 'nullable|string|size:18|unique:pegawai,NIP_NRP,' . $this->route('pegawai') . ',ID_PEGAWAI',
            'NAMA_PEGAWAI' => 'nullable|string|max:128',
            'JENIS_KELAMIN' => 'nullable|in:L,P',
            'TGL_LAHIR' => 'nullable|date|before:today',
            'ID_UNIT' => 'nullable|integer|exists:unit_kerja,ID_UNIT',
            'ID_JABATAN' => 'nullable|integer|exists:jabatan,ID_JABATAN',
            'ID_PANGKAT' => 'nullable|integer|exists:pangkat_golongan,ID_PANGKAT',
        ];
    }
}
