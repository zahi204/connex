<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum WorkArea: string implements HasLabel
{
    case North = 'north';
    case Center = 'center';
    case South = 'south';
    case Flexible = 'flexible';

    public function getLabel(): string
    {
        return match ($this) {
            self::North => 'צפון',
            self::Center => 'מרכז',
            self::South => 'דרום',
            self::Flexible => 'גמיש',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::North => 'North',
            self::Center => 'Center',
            self::South => 'South',
            self::Flexible => 'Flexible',
        };
    }
}
