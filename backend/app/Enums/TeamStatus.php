<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TeamStatus: string implements HasLabel
{
    case Available = 'available';
    case Assigned = 'assigned';
    case AvailableSoon = 'available_soon';
    case InAssembly = 'in_assembly';
    case Frozen = 'frozen';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'זמין',
            self::Assigned => 'משובץ',
            self::AvailableSoon => 'זמין בקרוב',
            self::InAssembly => 'בהרכבה',
            self::Frozen => 'מוקפא',
        };
    }

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
