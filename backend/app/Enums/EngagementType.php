<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EngagementType: string implements HasLabel
{
    case Daily = 'daily';
    case Contract = 'contract';

    public function getLabel(): string
    {
        return match ($this) {
            self::Daily => 'יומי',
            self::Contract => 'חוזה',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Daily',
            self::Contract => 'Contract',
        };
    }
}
