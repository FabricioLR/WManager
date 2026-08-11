<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendTemplateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone_number'  => ['required', 'string'],
            'template_name' => ['required', 'string'],
            'language_code' => ['nullable', 'string'],
            'components'    => ['nullable', 'array'], 
        ];
    }
}