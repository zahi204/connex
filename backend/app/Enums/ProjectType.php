<?php

namespace App\Enums;

enum ProjectType: string
{
    case Residential = 'residential';
    case Commercial = 'commercial';
    case Infrastructure = 'infrastructure';
    case Transportation = 'transportation';
    case Public = 'public';

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
