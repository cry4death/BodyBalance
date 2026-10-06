<?php

namespace App\Filament\Resources\SeoPages\Schemas;

use App\Filament\Schemas\SeoFields;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeoPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('route_name')
                            ->label('Страница')
                            ->options(fn (): array => array_map(
                                fn (array $page): string => $page['label'],
                                config('seo.pages'),
                            ))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->validationMessages(['unique' => 'Для этой страницы SEO уже заполнено.']),
                        ...SeoFields::fields(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
