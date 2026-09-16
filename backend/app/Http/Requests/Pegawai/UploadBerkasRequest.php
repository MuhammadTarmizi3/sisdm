<?php

namespace App\Http\Requests\Pegawai;

use Illuminate\Foundation\Http\FormRequest;

class UploadBerkasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ID_PERSYARATAN' => 'required|integer|exists:persyaratan_master,ID_PERSYARATAN',
        ];
    }
}
