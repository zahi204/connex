<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectType: string implements HasLabel
{
    case Residential = 'residential';
    case Commercial = 'commercial';
    case Infrastructure = 'infrastructure';
    case Transportation = 'transportation';
    case Public = 'public';

    public function getLabel(): string
    {
        return match ($this) {
            self::Residential => 'מגורים',
            self::Commercial => 'מסחרי',
            self::Infrastructure => 'תשתית',
            self::Transportation => 'תחבורה',
            self::Public => 'ציבורי',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Residential => 'Residential',
            self::Commercial => 'Commercial',
            self::Infrastructure => 'Infrastructure',
            self::Transportation => 'Transportation',
            self::Public => 'Public',
        };
    }
}
