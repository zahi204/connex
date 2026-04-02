<?php

namespace App\Enums;

enum ApprovalActionType: string
{
    case ProfileUpdate = 'profile_update';
    case ProjectSubmission = 'project_submission';
    case DocumentUpload = 'document_upload';
    case AvailabilityChange = 'availability_change';
    case BoqRequest = 'boq_request';

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
