<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BoqUrgency: string implements HasLabel
{
    case Standard = 'standard';
    case Urgent = 'urgent';
    case Express = 'express';

    public function getLabel(): string
    {
        return match ($this) {
            self::Standard => 'רגיל',
            self::Urgent => 'דחוף',
            self::Express => 'אקספרס',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Urgent => 'Urgent',
            self::Express => 'Express',
        };
    }
}
