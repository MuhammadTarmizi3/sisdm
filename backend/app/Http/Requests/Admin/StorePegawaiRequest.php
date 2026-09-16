<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'NIP_NRP' => 'required|string|size:18|unique:pegawai,NIP_NRP',
            'NAMA_PEGAWAI' => 'required|string|max:128',
            'JENIS_KELAMIN' => 'required|in:L,P',
            'TGL_LAHIR' => 'nullable|date|before:today',
            'ID_UNIT' => 'nullable|integer|exists:unit_kerja,ID_UNIT',
            'ID_JABATAN' => 'nullable|integer|exists:jabatan,ID_JABATAN',
            'ID_PANGKAT' => 'nullable|integer|exists:pangkat_golongan,ID_PANGKAT',
        ];
    }
}
