<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'message'      => ['required', 'string', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'The phone number must contain between 10 and 15 digits without symbols.',
        ];
    }
}
