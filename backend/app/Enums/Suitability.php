<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Suitability: string implements HasLabel
{
    case Suitable = 'suitable';
    case Partial = 'partial';
    case NotSuitable = 'not_suitable';

    public function getLabel(): string
    {
        return match ($this) {
            self::Suitable => 'מתאים',
            self::Partial => 'חלקי',
            self::NotSuitable => 'לא מתאים',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Suitable => 'Suitable',
            self::Partial => 'Partial',
            self::NotSuitable => 'Not Suitable',
        };
    }
}
