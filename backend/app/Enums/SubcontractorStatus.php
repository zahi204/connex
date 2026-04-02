<?php

namespace App\Enums;

enum SubcontractorStatus: string
{
    case Available = 'available';
    case Busy = 'busy';
    case AvailableSoon = 'available_soon';
    case Inactive = 'inactive';

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
