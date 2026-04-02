<?php

namespace App\Enums;

enum Suitability: string
{
    case Suitable = 'suitable';
    case Partial = 'partial';
    case NotSuitable = 'not_suitable';

    public function label(): string
    {
        return match ($this) {
            self::Suitable => 'Suitable',
            self::Partial => 'Partial',
            self::NotSuitable => 'Not Suitable',
        };
    }
}
