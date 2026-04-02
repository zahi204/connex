<?php

namespace App\Enums;

enum WorkArea: string
{
    case North = 'north';
    case Center = 'center';
    case South = 'south';
    case Flexible = 'flexible';

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
