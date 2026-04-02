<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class SelectRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'in:worker,developer,subcontractor,agency'],
        ];
    }
}
