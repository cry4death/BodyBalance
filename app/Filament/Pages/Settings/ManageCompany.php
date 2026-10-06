<?php

namespace App\Filament\Pages\Settings;

use App\Domain\Site\Settings\CompanySettings;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageCompany extends SettingsPage
{
    protected static string $settings = CompanySettings::class;

    protected static ?string $slug = 'settings/company';

    protected static ?string $title = 'Реквизиты продавца';

    protected static ?string $navigationLabel = 'Реквизиты';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Настройки сайта';

    protected static ?int $navigationSort = 2;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->description('Обязательная информация интернет-магазина в РБ. Выводится в подвале и в оферте; её же проверяет банк при подключении онлайн-оплаты.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('legal_name')
                            ->label('Продавец')
                            ->placeholder('ИП Фамилия Имя Отчество / ООО «Название»')
                            ->columnSpanFull(),
                        TextInput::make('unp')
                            ->label('УНП')
                            ->regex('/^[0-9A-Z]{9}$/')
                            ->validationMessages(['regex' => 'УНП состоит из 9 символов.'])
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? mb_strtoupper(trim($state)) : null),
                        TextInput::make('legal_address')
                            ->label('Юридический адрес'),
                        Textarea::make('registration_info')
                            ->label('Сведения о регистрации')
                            ->placeholder('Зарегистрирован … решением … от …')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('trade_register_number')
                            ->label('Номер в Торговом реестре'),
                        DatePicker::make('trade_register_date')
                            ->label('Дата включения в Торговый реестр')
                            ->displayFormat('d.m.Y'),
                    ]),
            ]);
    }
}
