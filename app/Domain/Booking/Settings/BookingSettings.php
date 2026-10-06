<?php

namespace App\Domain\Booking\Settings;

use App\Domain\Booking\Enums\BookingMode;
use Spatie\LaravelSettings\Settings;

/**
 * YCLIENTS booking and the manual «phone only» switch for when the widget misbehaves.
 * Exact widget embedding is finalized at the booking stage, once the YCLIENTS account is available.
 */
class BookingSettings extends Settings
{
    public BookingMode $mode;

    public ?string $yclients_company_id;

    /** Online booking page, e.g. https://n123456.yclients.com/ */
    public ?string $widget_url;

    /** Client account in YCLIENTS — the «Открыть кабинет YCLIENTS» link. */
    public ?string $client_cabinet_url;

    /** Shown instead of the widget in «phone only» mode or when YCLIENTS does not respond. */
    public string $fallback_message;

    public static function group(): string
    {
        return 'booking';
    }

    public function isOnlineBookingEnabled(): bool
    {
        return $this->mode === BookingMode::Widget && filled($this->widget_url);
    }
}
