<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProjectStatus: string implements HasLabel
{
    case New = 'new';
    case UnderReview = 'under_review';
    case ResourcesAssigned = 'resources_assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'חדש',
            self::UnderReview => 'בבדיקה',
            self::ResourcesAssigned => 'משאבים שובצו',
            self::InProgress => 'בתהליך',
            self::Completed => 'הושלם',
            self::Cancelled => 'בוטל',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::UnderReview => 'Under Review',
            self::ResourcesAssigned => 'Resources Assigned',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
