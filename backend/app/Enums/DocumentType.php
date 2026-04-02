<?php

namespace App\Enums;

enum DocumentType: string
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
