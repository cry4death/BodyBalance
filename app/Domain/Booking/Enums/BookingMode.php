<?php

namespace App\Domain\Booking\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookingMode: string implements HasLabel
{
    case Widget = 'widget';
    case PhoneOnly = 'phone_only';

    public function getLabel(): string
    {
        return match ($this) {
            self::Widget => 'Онлайн-запись через YCLIENTS',
            self::PhoneOnly => 'Только по телефону и в мессенджерах',
        };
    }
}
