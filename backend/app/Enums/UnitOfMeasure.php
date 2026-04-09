<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UnitOfMeasure: string implements HasLabel
{
    case Sqm = 'sqm';
    case LinearM = 'linear_m';
    case CubicM = 'cubic_m';
    case Unit = 'unit';
    case Ton = 'ton';
    case Kg = 'kg';
    case LumpSum = 'lump_sum';

    public function getLabel(): string
    {
        return match ($this) {
            self::Sqm => 'מ"ר',
            self::LinearM => 'מ"א',
            self::CubicM => 'מ"ק',
            self::Unit => 'יחידה',
            self::Ton => 'טון',
            self::Kg => 'ק"ג',
            self::LumpSum => 'סכום כולל',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Sqm => 'Square Meter',
            self::LinearM => 'Linear Meter',
            self::CubicM => 'Cubic Meter',
            self::Unit => 'Unit',
            self::Ton => 'Ton',
            self::Kg => 'Kilogram',
            self::LumpSum => 'Lump Sum',
        };
    }
}
