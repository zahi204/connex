<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Paid = 'paid';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Paid => 'Paid',
            self::Overdue => 'Overdue',
        };
    }
}
