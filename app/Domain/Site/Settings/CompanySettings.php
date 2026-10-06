<?php

namespace App\Domain\Site\Settings;

use Illuminate\Support\Carbon;
use Spatie\LaravelSettings\Settings;

/**
 * Seller details that a Belarusian online shop must publish (footer, offer, checkout).
 */
class CompanySettings extends Settings
{
    /** «ИП Фамилия Имя Отчество» or «ООО „Название“». */
    public ?string $legal_name;

    public ?string $unp;

    public ?string $legal_address;

    /** Who registered the business and when (certificate details). */
    public ?string $registration_info;

    public ?string $trade_register_number;

    /** Y-m-d. */
    public ?string $trade_register_date;

    public static function group(): string
    {
        return 'company';
    }

    public function tradeRegisterLine(): ?string
    {
        if (! $this->trade_register_number) {
            return null;
        }

        $date = $this->trade_register_date
            ? ' от '.Carbon::parse($this->trade_register_date)->format('d.m.Y')
            : '';

        return "Зарегистрирован в Торговом реестре Республики Беларусь № {$this->trade_register_number}{$date}";
    }
}
