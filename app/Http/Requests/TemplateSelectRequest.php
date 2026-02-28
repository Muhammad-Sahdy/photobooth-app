<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateSelectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_id' => ['required', 'exists:templates,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'template_id.required' => 'Template wajib dipilih.',
            'template_id.exists'   => 'Template tidak ditemukan.',
        ];
    }
}
