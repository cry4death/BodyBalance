<?php

namespace App\Filament\Schemas;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

/**
 * SEO form fields shared by the «SEO страниц» resource and every model with its own page.
 * In a model resource: SeoFields::section() — saves into the model's `seo` relation.
 */
final class SeoFields
{
    public static function section(): Section
    {
        return Section::make('SEO')
            ->description('Пустые поля заполнятся автоматически из названия и описания.')
            ->relationship('seo')
            ->schema(self::fields())
            ->collapsible()
            ->collapsed();
    }

    /**
     * @return array<Component|Field>
     */
    public static function fields(): array
    {
        return [
            TextInput::make('title')
                ->label('Title')
                ->maxLength(255)
                ->live(debounce: 500)
                ->hint(fn (?string $state): string => self::length($state))
                ->helperText('Заголовок во вкладке браузера и в поиске. Оптимально до 60–70 символов. Используется как есть, без добавления названия студии.'),
            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->maxLength(500)
                ->live(debounce: 500)
                ->hint(fn (?string $state): string => self::length($state))
                ->helperText('Описание в поисковой выдаче. Оптимально 140–160 символов.'),
            TextInput::make('h1')
                ->label('H1')
                ->maxLength(255)
                ->helperText('Главный заголовок на странице.'),
            TextInput::make('canonical_url')
                ->label('Canonical URL')
                ->url()
                ->maxLength(255)
                ->helperText('Заполнять, только если страница дублирует другую. Обычно оставляют пустым.'),
            FileUpload::make('og_image')
                ->label('Картинка для соцсетей и мессенджеров')
                ->image()
                ->disk('public')
                ->directory('seo')
                ->maxSize(2048)
                ->helperText('Рекомендуемый размер 1200×630 px.'),
            Toggle::make('noindex')
                ->label('Скрыть страницу от поисковиков (noindex)'),
        ];
    }

    private static function length(?string $state): string
    {
        return mb_strlen((string) $state).' симв.';
    }
}
