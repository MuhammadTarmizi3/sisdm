<?php

namespace App\Http\Requests\Pegawai;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanKgbRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'GAJI_POKOK_LAMA' => 'required|integer',
            'GAJI_POKOK_BARU' => 'required|integer',
        ];
    }
}
