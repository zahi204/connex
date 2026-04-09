<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NoteType: string implements HasLabel
{
    case Note = 'note';
    case PhoneCall = 'phone_call';
    case Meeting = 'meeting';
    case Decision = 'decision';

    public function getLabel(): string
    {
        return match ($this) {
            self::Note => 'הערה',
            self::PhoneCall => 'שיחת טלפון',
            self::Meeting => 'פגישה',
            self::Decision => 'החלטה',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Note => 'Note',
            self::PhoneCall => 'Phone Call',
            self::Meeting => 'Meeting',
            self::Decision => 'Decision',
        };
    }
}
