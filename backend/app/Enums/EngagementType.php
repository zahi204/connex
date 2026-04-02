<?php

namespace App\Enums;

enum EngagementType: string
{
    case Daily = 'daily';
    case Contract = 'contract';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Daily',
            self::Contract => 'Contract',
        };
    }
}
