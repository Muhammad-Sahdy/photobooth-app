<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotoSelectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Kita biarkan max 4 sesuai batas maksimal template anda
            'photo_ids'   => 'required|array|min:1|max:4',
            'photo_ids.*' => 'integer|exists:photos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'photo_ids.required' => 'Silakan pilih foto sesuai jumlah slot.',
            'photo_ids.array'    => 'Format data foto tidak valid.',
            'photo_ids.min'      => 'Minimal pilih :min foto.',
            'photo_ids.*.exists' => 'Foto yang dipilih tidak ditemukan.',
        ];
    }
}
