<?php

namespace App\Enums;

enum WorkerStatus: string
{
    case Available = 'available';
    case Assigned = 'assigned';
    case FutureAssignment = 'future_assignment';
    case InTraining = 'in_training';
    case Waiting = 'waiting';
    case Inactive = 'inactive';
    case Frozen = 'frozen';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Assigned => 'Assigned',
            self::FutureAssignment => 'Future Assignment',
            self::InTraining => 'In Training',
            self::Waiting => 'Waiting',
            self::Inactive => 'Inactive',
            self::Frozen => 'Frozen',
        };
    }
}
