<?php

namespace App\Actions\Boq;

use App\Models\BoqRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class BoqExportAction implements FromCollection, WithHeadings
{
    private BoqRequest $request;

    public function forRequest(BoqRequest $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function collection()
    {
        return $this->request->lineItems()
            ->orderBy('trade')
            ->get()
            ->map(fn ($item) => [
                'Trade' => $item->trade,
                'Description' => $item->description,
                'Unit' => $item->unit_of_measure?->value,
                'Quantity' => $item->quantity,
                'Unit Price' => $item->unit_price,
                'Total' => $item->total_cost,
            ]);
    }

    public function headings(): array
    {
        return ['Trade', 'Description', 'Unit', 'Quantity', 'Unit Price', 'Total'];
    }

    public function export(BoqRequest $request)
    {
        $this->request = $request;
        return Excel::download($this, "boq-{$request->id}.xlsx");
    }
}
