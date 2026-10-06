<?php

namespace App\Filament\Resources\SeoPages;

use App\Domain\Seo\Models\SeoMeta;
use App\Filament\Resources\SeoPages\Pages\CreateSeoPage;
use App\Filament\Resources\SeoPages\Pages\EditSeoPage;
use App\Filament\Resources\SeoPages\Pages\ListSeoPages;
use App\Filament\Resources\SeoPages\Schemas\SeoPageForm;
use App\Filament\Resources\SeoPages\Tables\SeoPagesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * SEO of fixed pages (home, shop, cart...). Model pages edit SEO in their own forms.
 */
class SeoPageResource extends Resource
{
    protected static ?string $model = SeoMeta::class;

    protected static ?string $slug = 'seo-pages';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'SEO';

    protected static ?string $navigationLabel = 'SEO страниц';

    protected static ?string $modelLabel = 'SEO страницы';

    protected static ?string $pluralModelLabel = 'SEO страниц';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SeoPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoPagesTable::configure($table);
    }

    /**
     * Only fixed pages; SEO rows of products/services belong to their own forms.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNotNull('route_name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoPages::route('/'),
            'create' => CreateSeoPage::route('/create'),
            'edit' => EditSeoPage::route('/{record}/edit'),
        ];
    }
}
