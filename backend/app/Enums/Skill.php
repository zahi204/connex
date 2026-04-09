<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Skill: string implements HasLabel
{
    case Formwork = 'formwork';
    case Rebar = 'rebar';
    case General = 'general';
    case Finishing = 'finishing';
    case Skeleton = 'skeleton';
    case Electrical = 'electrical';
    case Plumbing = 'plumbing';
    case Painting = 'painting';
    case Waterproofing = 'waterproofing';

    public function getLabel(): string
    {
        return match ($this) {
            self::Formwork => 'קופסאות',
            self::Rebar => 'ברזל',
            self::General => 'כללי',
            self::Finishing => 'גימור',
            self::Skeleton => 'שלד',
            self::Electrical => 'חשמל',
            self::Plumbing => 'אינסטלציה',
            self::Painting => 'צביעה',
            self::Waterproofing => 'איטום',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Formwork => 'Formwork',
            self::Rebar => 'Rebar',
            self::General => 'General',
            self::Finishing => 'Finishing',
            self::Skeleton => 'Skeleton',
            self::Electrical => 'Electrical',
            self::Plumbing => 'Plumbing',
            self::Painting => 'Painting',
            self::Waterproofing => 'Waterproofing',
        };
    }
}
