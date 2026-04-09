<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SubcontractorStatus: string implements HasLabel
{
    case Available = 'available';
    case Busy = 'busy';
    case AvailableSoon = 'available_soon';
    case Inactive = 'inactive';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'זמין',
            self::Busy => 'עסוק',
            self::AvailableSoon => 'זמין בקרוב',
            self::Inactive => 'לא פעיל',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Busy => 'Busy',
            self::AvailableSoon => 'Available Soon',
            self::Inactive => 'Inactive',
        };
    }
}
