<?php

namespace App\Filament\Pages\Settings;

use App\Domain\Booking\Enums\BookingMode;
use App\Domain\Booking\Settings\BookingSettings;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageBooking extends SettingsPage
{
    protected static string $settings = BookingSettings::class;

    protected static ?string $slug = 'settings/booking';

    protected static ?string $title = 'Онлайн-запись';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Настройки сайта';

    protected static ?int $navigationSort = 4;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Режим записи')
                    ->schema([
                        ToggleButtons::make('mode')
                            ->hiddenLabel()
                            ->options(BookingMode::class)
                            ->required()
                            ->inline()
                            ->helperText('Если с YCLIENTS что-то не так, переключите на «Только по телефону» — кнопки «Записаться» покажут контакты вместо виджета.'),
                        Textarea::make('fallback_message')
                            ->label('Сообщение вместо виджета')
                            ->rows(2)
                            ->required()
                            ->helperText('Показывается в режиме «только по телефону» и когда YCLIENTS не отвечает. Телефон и мессенджеры добавятся автоматически.'),
                    ]),
                Section::make('YCLIENTS')
                    ->description('Данные из личного кабинета YCLIENTS, раздел «Онлайн-запись».')
                    ->columns(2)
                    ->schema([
                        TextInput::make('yclients_company_id')
                            ->label('ID компании')
                            ->regex('/^\d+$/')
                            ->validationMessages(['regex' => 'ID компании состоит из цифр.']),
                        TextInput::make('widget_url')
                            ->label('Ссылка на онлайн-запись')
                            ->url()
                            ->placeholder('https://n123456.yclients.com/'),
                        TextInput::make('client_cabinet_url')
                            ->label('Ссылка на личный кабинет клиента')
                            ->url()
                            ->helperText('Для кнопки «Открыть кабинет YCLIENTS» в личном кабинете покупателя.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
