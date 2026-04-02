<?php

namespace App\Enums;

enum TeamStatus: string
{
    case Available = 'available';
    case Assigned = 'assigned';
    case AvailableSoon = 'available_soon';
    case InAssembly = 'in_assembly';
    case Frozen = 'frozen';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Assigned => 'Assigned',
            self::AvailableSoon => 'Available Soon',
            self::InAssembly => 'In Assembly',
            self::Frozen => 'Frozen',
        };
    }
}
