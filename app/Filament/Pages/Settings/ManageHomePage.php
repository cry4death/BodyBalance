<?php

namespace App\Filament\Pages\Settings;

use App\Domain\Studio\Settings\HomePageSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageHomePage extends SettingsPage
{
    protected static string $settings = HomePageSettings::class;

    protected static ?string $slug = 'settings/home';

    protected static ?string $title = 'Главная страница';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'Настройки сайта';

    protected static ?int $navigationSort = 3;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Первый экран')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('hero_slogan')
                            ->label('Слоган')
                            ->required()
                            ->maxLength(150),
                        FileUpload::make('hero_image')
                            ->label('Фото')
                            ->image()
                            ->disk('public')
                            ->directory('home')
                            ->maxSize(5120)
                            ->helperText('Горизонтальное фото не меньше 1920 px по ширине, до 5 МБ.'),
                        TextInput::make('hero_image_alt')
                            ->label('Описание фото (alt)')
                            ->helperText('Что изображено — для поисковиков и незрячих посетителей.')
                            ->requiredWith('hero_image'),
                    ]),
                Section::make('О студии')
                    ->schema([
                        TextInput::make('about_title')
                            ->label('Заголовок блока')
                            ->required()
                            ->maxLength(100),
                        Textarea::make('about_text')
                            ->label('Текст')
                            ->rows(8)
                            ->helperText('2–3 абзаца. Абзацы разделяйте пустой строкой.'),
                        Repeater::make('about_gallery')
                            ->label('Фото интерьера')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Фото')
                                    ->image()
                                    ->disk('public')
                                    ->directory('home/gallery')
                                    ->maxSize(5120)
                                    ->required(),
                                TextInput::make('alt')
                                    ->label('Описание фото (alt)')
                                    ->required(),
                            ])
                            ->grid(3)
                            ->maxItems(12)
                            ->defaultItems(0)
                            ->addActionLabel('Добавить фото')
                            ->reorderableWithButtons(),
                    ]),
            ]);
    }
}
