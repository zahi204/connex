<?php

namespace App\Enums;

enum ProjectSource: string
{
    case Manual = 'manual';
    case External = 'external';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manual',
            self::External => 'External',
        };
    }
}
