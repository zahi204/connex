<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectSource: string implements HasLabel
{
    case Manual = 'manual';
    case External = 'external';

    public function getLabel(): string
    {
        return match ($this) {
            self::Manual => 'ידני',
            self::External => 'חיצוני',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manual',
            self::External => 'External',
        };
    }
}
