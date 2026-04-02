<?php

namespace App\Actions\Boq;

use App\Models\BoqRequest;
use App\Models\User;

class AcceptBoqRequestAction
{
    public function execute(BoqRequest $request, User $preparer): BoqRequest
    {
        $request->update([
            'status' => 'in_preparation',
            'prepared_by_user_id' => $preparer->id,
        ]);

        return $request->fresh();
    }
}
