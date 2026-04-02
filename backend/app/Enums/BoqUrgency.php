<?php

namespace App\Enums;

enum BoqUrgency: string
{
    case Standard = 'standard';
    case Urgent = 'urgent';
    case Express = 'express';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Urgent => 'Urgent',
            self::Express => 'Express',
        };
    }
}
