<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ApprovalActionType: string implements HasLabel
{
    case ProfileUpdate = 'profile_update';
    case ProjectSubmission = 'project_submission';
    case DocumentUpload = 'document_upload';
    case AvailabilityChange = 'availability_change';
    case BoqRequest = 'boq_request';

    public function getLabel(): string
    {
        return match ($this) {
            self::ProfileUpdate => 'עדכון פרופיל',
            self::ProjectSubmission => 'הגשת פרויקט',
            self::DocumentUpload => 'העלאת מסמך',
            self::AvailabilityChange => 'שינוי זמינות',
            self::BoqRequest => 'בקשת כתב כמויות',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ProfileUpdate => 'Profile Update',
            self::ProjectSubmission => 'Project Submission',
            self::DocumentUpload => 'Document Upload',
            self::AvailabilityChange => 'Availability Change',
            self::BoqRequest => 'BOQ Request',
        };
    }
}
