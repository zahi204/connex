<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TrainingCycleStatus: string implements HasLabel
{
    case Planned = 'planned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Planned => 'מתוכנן',
            self::InProgress => 'בתהליך',
            self::Completed => 'הושלם',
            self::Cancelled => 'בוטל',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Planned',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
