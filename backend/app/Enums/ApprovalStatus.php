<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ApprovalStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case ChangesRequired = 'changes_required';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'ממתין',
            self::Approved => 'אושר',
            self::Rejected => 'נדחה',
            self::ChangesRequired => 'נדרשים שינויים',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::ChangesRequired => 'Changes Required',
        };
    }
}
