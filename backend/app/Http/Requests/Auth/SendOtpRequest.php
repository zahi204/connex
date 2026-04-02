<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class SendOtpRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+972[0-9]{8,9}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone number must be in E.164 format (+972XXXXXXXXX).',
        ];
    }
}
