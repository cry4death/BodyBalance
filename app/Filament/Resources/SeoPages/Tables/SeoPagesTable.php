<?php

namespace App\Filament\Resources\SeoPages\Tables;

use App\Domain\Seo\Models\SeoMeta;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('route_name')
                    ->label('Страница')
                    ->formatStateUsing(fn (SeoMeta $record): string => $record->pageLabel()),
                TextColumn::make('title')
                    ->label('Title')
                    ->placeholder('авто')
                    ->limit(60)
                    ->searchable(),
                IconColumn::make('noindex')
                    ->label('Скрыта')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Изменено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->emptyStateHeading('SEO страниц ещё не заполнено')
            ->emptyStateDescription('Пока поля пустые, сайт подставляет значения по умолчанию.');
    }
}
