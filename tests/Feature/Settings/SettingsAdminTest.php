<?php

use App\Domain\Booking\Enums\BookingMode;
use App\Domain\Booking\Settings\BookingSettings;
use App\Domain\Site\Settings\CompanySettings;
use App\Domain\Site\Settings\ContactSettings;
use App\Domain\Studio\Settings\HomePageSettings;
use App\Filament\Pages\Settings\ManageBooking;
use App\Filament\Pages\Settings\ManageCompany;
use App\Filament\Pages\Settings\ManageContacts;
use App\Filament\Pages\Settings\ManageHomePage;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->actingAs(User::factory()->create()->assignRole('owner'));
});

it('opens every settings page', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/admin/settings/contacts', '/admin/settings/company', '/admin/settings/home', '/admin/settings/booking']);

it('saves contacts and normalizes messenger usernames', function () {
    Livewire::test(ManageContacts::class)
        ->fillForm([
            'phone' => '+375 29 123-45-67',
            'email' => 'hello@bodybalance.by',
            'telegram_username' => 'https://t.me/bodybalance_studio',
            'instagram_username' => '@body.balance',
            'latitude' => '53.98',
            'longitude' => '27.64',
            'opening_hours' => [
                ['days' => ['mo', 'tu', 'we', 'th', 'fr'], 'opens' => '09:00', 'closes' => '21:00'],
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $contacts = app(ContactSettings::class)->refresh();

    expect($contacts->phone)->toBe('+375 29 123-45-67')
        ->and($contacts->telegram_username)->toBe('bodybalance_studio')
        ->and($contacts->instagram_username)->toBe('body.balance')
        ->and($contacts->latitude)->toBe(53.98)
        ->and($contacts->openingHoursLines())->toBe(['Пн–Пт: 09:00–21:00']);
});

it('rejects a non-Belarusian phone and a broken Telegram name', function () {
    Livewire::test(ManageContacts::class)
        ->fillForm(['phone' => '+7 999 123-45-67', 'telegram_username' => 'имя с пробелами'])
        ->call('save')
        ->assertHasFormErrors(['phone', 'telegram_username']);
});

it('validates the UNP', function () {
    Livewire::test(ManageCompany::class)
        ->fillForm(['unp' => '12345'])
        ->call('save')
        ->assertHasFormErrors(['unp']);

    Livewire::test(ManageCompany::class)
        ->fillForm(['unp' => '191234567', 'trade_register_number' => '123456', 'trade_register_date' => '2026-09-15'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(CompanySettings::class)->refresh()->tradeRegisterLine())->toContain('№ 123456 от 15.09.2026');
});

it('saves home page texts', function () {
    Livewire::test(ManageHomePage::class)
        ->fillForm(['hero_slogan' => 'Новый слоган', 'about_text' => "Абзац один.\n\nАбзац два."])
        ->call('save')
        ->assertHasNoFormErrors();

    $home = app(HomePageSettings::class)->refresh();

    expect($home->hero_slogan)->toBe('Новый слоган')
        ->and($home->aboutParagraphs())->toHaveCount(2);
});

it('switches booking to phone-only mode', function () {
    Livewire::test(ManageBooking::class)
        ->fillForm(['mode' => BookingMode::PhoneOnly->value, 'widget_url' => 'https://n123456.yclients.com/'])
        ->call('save')
        ->assertHasNoFormErrors();

    $booking = app(BookingSettings::class)->refresh();

    expect($booking->mode)->toBe(BookingMode::PhoneOnly)
        ->and($booking->isOnlineBookingEnabled())->toBeFalse();
});
