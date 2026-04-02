<?php

namespace App\Enums;

enum PaymentType: string
{
    case WorkerSubscription = 'worker_subscription';
    case TrainingFee = 'training_fee';
    case Brokerage = 'brokerage';
    case ManagementFee = 'management_fee';
    case BoqService = 'boq_service';

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
