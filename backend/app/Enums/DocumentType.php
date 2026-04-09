<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasLabel
{
    case Passport = 'passport';
    case Visa = 'visa';
    case License = 'license';
    case Insurance = 'insurance';
    case SafetyCert = 'safety_cert';
    case Plan = 'plan';
    case Permit = 'permit';
    case Certificate = 'certificate';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Passport => 'דרכון',
            self::Visa => 'ויזה',
            self::License => 'רישיון',
            self::Insurance => 'ביטוח',
            self::SafetyCert => 'תעודת בטיחות',
            self::Plan => 'תוכנית',
            self::Permit => 'היתר',
            self::Certificate => 'תעודה',
            self::Other => 'אחר',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Passport => 'Passport',
            self::Visa => 'Visa',
            self::License => 'License',
            self::Insurance => 'Insurance',
            self::SafetyCert => 'Safety Certificate',
            self::Plan => 'Plan',
            self::Permit => 'Permit',
            self::Certificate => 'Certificate',
            self::Other => 'Other',
        };
    }
}
