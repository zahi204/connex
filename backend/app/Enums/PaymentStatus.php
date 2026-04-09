<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel
{
    case Paid = 'paid';
    case Overdue = 'overdue';

    public function getLabel(): string
    {
        return match ($this) {
            self::Paid => 'שולם',
            self::Overdue => 'באיחור',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Paid => 'Paid',
            self::Overdue => 'Overdue',
        };
    }
}
