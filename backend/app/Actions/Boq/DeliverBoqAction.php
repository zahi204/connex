<?php

namespace App\Actions\Boq;

use App\Models\BoqRequest;

class DeliverBoqAction
{
    public function execute(BoqRequest $request): BoqRequest
    {
        $total = $request->lineItems()->sum('total_cost');

        $request->update([
            'status' => 'delivered',
            'price' => $total,
            'delivered_at' => now(),
        ]);

        return $request->fresh();
    }
}
