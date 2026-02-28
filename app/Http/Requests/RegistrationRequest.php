<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Jika nanti ada auth khusus bisa diubah, untuk sekarang izinkan semua
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
        ];
    }
}
