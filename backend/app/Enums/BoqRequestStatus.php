<?php

namespace App\Enums;

enum BoqRequestStatus: string
{
    case New = 'new';
    case UnderReview = 'under_review';
    case InPreparation = 'in_preparation';
    case ReadyForReview = 'ready_for_review';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

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
