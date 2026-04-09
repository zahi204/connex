<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BoqRequestStatus: string implements HasLabel
{
    case New = 'new';
    case UnderReview = 'under_review';
    case InPreparation = 'in_preparation';
    case ReadyForReview = 'ready_for_review';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'חדש',
            self::UnderReview => 'בבדיקה',
            self::InPreparation => 'בהכנה',
            self::ReadyForReview => 'מוכן לבדיקה',
            self::Delivered => 'נמסר',
            self::Cancelled => 'בוטל',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::UnderReview => 'Under Review',
            self::InPreparation => 'In Preparation',
            self::ReadyForReview => 'Ready for Review',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }
}
