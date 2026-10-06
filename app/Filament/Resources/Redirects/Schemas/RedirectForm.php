<?php

namespace App\Filament\Resources\Redirects\Schemas;

use App\Domain\Seo\Models\Redirect;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->description('Редирект срабатывает, только если по старому адресу страницы больше нет (иначе открылась бы она).')
                    ->schema([
                        TextInput::make('from_path')
                            ->label('Старый адрес')
                            ->placeholder('/staryj-adres')
                            ->helperText('Путь без домена. Можно вставить полную ссылку — домен и параметры отбросятся.')
                            ->required()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn (?string $state): string => Redirect::normalizePath((string) $state))
                            ->unique(ignoreRecord: true)
                            ->validationMessages(['unique' => 'Для этого адреса редирект уже есть.']),
                        TextInput::make('to_url')
                            ->label('Новый адрес')
                            ->placeholder('/novyj-adres или https://...')
                            ->required()
                            ->maxLength(255)
                            ->rule(fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                if (Redirect::normalizePath((string) $value) === Redirect::normalizePath((string) $get('from_path'))) {
                                    $fail('Новый адрес совпадает со старым — получится бесконечный редирект.');
                                }
                            }),
                        Select::make('status_code')
                            ->label('Тип')
                            ->options(Redirect::STATUS_CODES)
                            ->default(301)
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
