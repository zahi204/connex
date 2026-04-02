<?php

namespace App\Enums;

enum NoteType: string
{
    case Note = 'note';
    case PhoneCall = 'phone_call';
    case Meeting = 'meeting';
    case Decision = 'decision';

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
