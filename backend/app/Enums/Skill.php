<?php

namespace App\Enums;

enum Skill: string
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
