<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum WorkerStatus: string implements HasLabel
{
    case Available = 'available';
    case Assigned = 'assigned';
    case FutureAssignment = 'future_assignment';
    case InTraining = 'in_training';
    case Waiting = 'waiting';
    case Inactive = 'inactive';
    case Frozen = 'frozen';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'זמין',
            self::Assigned => 'משובץ',
            self::FutureAssignment => 'שיבוץ עתידי',
            self::InTraining => 'בהכשרה',
            self::Waiting => 'ממתין',
            self::Inactive => 'לא פעיל',
            self::Frozen => 'מוקפא',
        };
    }

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
