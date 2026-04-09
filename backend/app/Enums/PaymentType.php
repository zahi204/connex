<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasLabel
{
    case WorkerSubscription = 'worker_subscription';
    case TrainingFee = 'training_fee';
    case Brokerage = 'brokerage';
    case ManagementFee = 'management_fee';
    case BoqService = 'boq_service';

    public function getLabel(): string
    {
        return match ($this) {
            self::WorkerSubscription => 'דמי רישום עובד',
            self::TrainingFee => 'דמי הכשרה',
            self::Brokerage => 'תיווך',
            self::ManagementFee => 'דמי ניהול',
            self::BoqService => 'שירות כתב כמויות',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::WorkerSubscription => 'Worker Subscription',
            self::TrainingFee => 'Training Fee',
            self::Brokerage => 'Brokerage',
            self::ManagementFee => 'Management Fee',
            self::BoqService => 'BOQ Service',
        };
    }
}
