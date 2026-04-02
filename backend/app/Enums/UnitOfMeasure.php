<?php

namespace App\Enums;

enum UnitOfMeasure: string
{
    case Sqm = 'sqm';
    case LinearM = 'linear_m';
    case CubicM = 'cubic_m';
    case Unit = 'unit';
    case Ton = 'ton';
    case Kg = 'kg';
    case LumpSum = 'lump_sum';

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
