<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case New = 'new';
    case UnderReview = 'under_review';
    case ResourcesAssigned = 'resources_assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

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
