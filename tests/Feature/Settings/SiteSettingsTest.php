<?php

use App\Domain\Booking\Enums\BookingMode;
use App\Domain\Booking\Settings\BookingSettings;
use App\Domain\Site\Settings\CompanySettings;
use App\Domain\Site\Settings\ContactSettings;
use App\Domain\Studio\Settings\HomePageSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('starts with the values from the spec', function () {
    expect(app(ContactSettings::class)->address)->toBe('д. Копище, ул. Леонардо да Винчи, 2')
        ->and(app(HomePageSettings::class)->hero_slogan)->toBe('Баланс тела и кожи в одной студии')
        ->and(app(BookingSettings::class)->mode)->toBe(BookingMode::Widget);
});

describe('contacts', function () {
    it('builds phone and messenger links', function () {
        $contacts = app(ContactSettings::class);
        $contacts->phone = '+375 (29) 123-45-67';
        $contacts->viber_phone = '+375 33 765-43-21';
        $contacts->telegram_username = 'bodybalance_studio';
        $contacts->instagram_username = 'body.balance';

        expect($contacts->phoneHref())->toBe('tel:+375291234567')
            ->and($contacts->viberUrl())->toBe('viber://chat?number=%2B375337654321')
            ->and($contacts->telegramUrl())->toBe('https://t.me/bodybalance_studio')
            ->and($contacts->instagramUrl())->toBe('https://www.instagram.com/body.balance/');
    });

    it('returns no links for empty contacts', function () {
        $contacts = app(ContactSettings::class);

        expect($contacts->phoneHref())->toBeNull()
            ->and($contacts->telegramUrl())->toBeNull()
            ->and($contacts->viberUrl())->toBeNull();
    });

    it('formats day ranges', function (array $days, string $expected) {
        expect(ContactSettings::formatDays($days))->toBe($expected);
    })->with([
        'weekdays' => [['mo', 'tu', 'we', 'th', 'fr'], 'Пн–Пт'],
        'weekend' => [['sa', 'su'], 'Сб, Вс'],
        'every day' => [['su', 'mo', 'tu', 'we', 'th', 'fr', 'sa'], 'Ежедневно'],
        'unordered with gaps' => [['fr', 'mo', 'tu', 'we'], 'Пн–Ср, Пт'],
        'single day' => [['sa'], 'Сб'],
    ]);

    it('formats opening hours lines', function () {
        $contacts = app(ContactSettings::class);
        $contacts->opening_hours = [
            ['days' => ['mo', 'tu', 'we', 'th', 'fr'], 'opens' => '09:00', 'closes' => '21:00'],
            ['days' => ['sa', 'su'], 'opens' => '10:00:00', 'closes' => '18:00:00'],
        ];

        expect($contacts->openingHoursLines())->toBe(['Пн–Пт: 09:00–21:00', 'Сб, Вс: 10:00–18:00']);
    });

    it('extracts usernames from pasted links', function (?string $input, ?string $expected) {
        expect(ContactSettings::normalizeUsername($input))->toBe($expected);
    })->with([
        ['@bodybalance', 'bodybalance'],
        ['https://t.me/bodybalance', 'bodybalance'],
        ['https://www.instagram.com/body.balance/', 'body.balance'],
        ['  ', null],
        [null, null],
    ]);
});

it('builds the trade register line', function () {
    $company = app(CompanySettings::class);
    $company->trade_register_number = '123456';
    $company->trade_register_date = '2026-09-15';

    expect($company->tradeRegisterLine())
        ->toBe('Зарегистрирован в Торговом реестре Республики Беларусь № 123456 от 15.09.2026');
});

it('splits the about text into paragraphs', function () {
    $home = app(HomePageSettings::class);
    $home->about_text = "Первый абзац.\r\n\r\nВторой абзац,\nв две строки.\n\n\n";

    expect($home->aboutParagraphs())->toBe(['Первый абзац.', "Второй абзац,\nв две строки."]);
});

it('enables online booking only with a widget link', function () {
    $booking = app(BookingSettings::class);

    expect($booking->isOnlineBookingEnabled())->toBeFalse();

    $booking->widget_url = 'https://n123456.yclients.com/';
    expect($booking->isOnlineBookingEnabled())->toBeTrue();

    $booking->mode = BookingMode::PhoneOnly;
    expect($booking->isOnlineBookingEnabled())->toBeFalse();
});
