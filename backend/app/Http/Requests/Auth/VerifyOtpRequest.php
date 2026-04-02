<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class VerifyOtpRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+972[0-9]{8,9}$/'],
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ];
    }
}
