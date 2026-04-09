<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssignmentStatus: string implements HasLabel
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Suspended = 'suspended';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'חדש',
            self::InProgress => 'בתהליך',
            self::Completed => 'הושלם',
            self::Cancelled => 'בוטל',
            self::Suspended => 'מושהה',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Suspended => 'Suspended',
        };
    }
}
