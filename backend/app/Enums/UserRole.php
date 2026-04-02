<?php

namespace App\Enums;

enum UserRole: string
{
    case Worker = 'worker';
    case Developer = 'developer';
    case Subcontractor = 'subcontractor';
    case Agency = 'agency';
    case Admin = 'admin';
    case Coordinator = 'coordinator';

    public function label(): string
    {
        return match ($this) {
            self::Worker => 'Worker',
            self::Developer => 'Developer / Large Contractor',
            self::Subcontractor => 'Subcontractor',
            self::Agency => 'Staffing Agency',
            self::Admin => 'System Admin',
            self::Coordinator => 'Coordinator',
        };
    }
}
