<?php

namespace App\Filament\Pages\Settings;

use App\Domain\Site\Settings\ContactSettings;
use BackedEnum;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageContacts extends SettingsPage
{
    protected static string $settings = ContactSettings::class;

    protected static ?string $slug = 'settings/contacts';

    protected static ?string $title = 'Контакты и соцсети';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static string|UnitEnum|null $navigationGroup = 'Настройки сайта';

    protected static ?int $navigationSort = 1;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Контакты')
                    ->description('Показываются в шапке, подвале и блоке «Контакты».')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->placeholder('+375 29 123-45-67')
                            ->rule(fn (): Closure => self::belarusianPhoneRule()),
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        TextInput::make('address')
                            ->label('Адрес')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('map_url')
                            ->label('Ссылка на карту')
                            ->url()
                            ->placeholder('https://yandex.by/maps/...')
                            ->helperText('Ссылка «Поделиться» из Яндекс Карт или Google Maps.')
                            ->columnSpanFull(),
                        TextInput::make('latitude')
                            ->label('Широта')
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->dehydrateStateUsing(fn (mixed $state): ?float => filled($state) ? (float) $state : null)
                            ->helperText('Для карты и микроразметки, например 53.9800.'),
                        TextInput::make('longitude')
                            ->label('Долгота')
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->dehydrateStateUsing(fn (mixed $state): ?float => filled($state) ? (float) $state : null)
                            ->helperText('Например 27.6400.'),
                    ]),
                Section::make('Режим работы')
                    ->schema([
                        Repeater::make('opening_hours')
                            ->hiddenLabel()
                            ->schema([
                                CheckboxList::make('days')
                                    ->label('Дни')
                                    ->options(ContactSettings::DAYS)
                                    ->columns(7)
                                    ->required()
                                    ->columnSpanFull(),
                                TimePicker::make('opens')
                                    ->label('С')
                                    ->seconds(false)
                                    ->required(),
                                TimePicker::make('closes')
                                    ->label('До')
                                    ->seconds(false)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => filled($state['days'] ?? null)
                                ? ContactSettings::formatDays($state['days'])
                                : null)
                            ->addActionLabel('Добавить строку')
                            ->defaultItems(0)
                            ->reorderableWithButtons(),
                    ]),
                Section::make('Мессенджеры и соцсети')
                    ->description('Можно вставить имя пользователя или ссылку на профиль — сохранится имя.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('telegram_username')
                            ->label('Telegram')
                            ->prefix('@')
                            ->rule(fn (): Closure => self::usernameRule('/^[A-Za-z0-9_]{5,32}$/'))
                            ->dehydrateStateUsing(fn (?string $state): ?string => ContactSettings::normalizeUsername($state)),
                        TextInput::make('viber_phone')
                            ->label('Viber (номер телефона)')
                            ->tel()
                            ->placeholder('+375 29 123-45-67')
                            ->rule(fn (): Closure => self::belarusianPhoneRule()),
                        TextInput::make('instagram_username')
                            ->label('Instagram')
                            ->prefix('@')
                            ->rule(fn (): Closure => self::usernameRule('/^[A-Za-z0-9._]{1,30}$/'))
                            ->dehydrateStateUsing(fn (?string $state): ?string => ContactSettings::normalizeUsername($state)),
                    ]),
            ]);
    }

    /**
     * Filament evaluates closures passed to rule(), so callers wrap these in fn (): Closure => ...
     */
    private static function belarusianPhoneRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (filled($value) && ! ContactSettings::isBelarusianPhone((string) $value)) {
                $fail('Укажите белорусский номер в формате +375 XX XXX-XX-XX.');
            }
        };
    }

    private static function usernameRule(string $pattern): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($pattern): void {
            $username = ContactSettings::normalizeUsername((string) $value);

            if ($username !== null && ! preg_match($pattern, $username)) {
                $fail('Похоже, это не имя пользователя. Вставьте ссылку на профиль или имя без пробелов.');
            }
        };
    }
}
