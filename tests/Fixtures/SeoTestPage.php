<?php

namespace Tests\Fixtures;

use App\Domain\Seo\Concerns\HasSeo;
use App\Domain\Seo\Contracts\InSitemap;
use App\Domain\Seo\Contracts\Seoable;
use App\Domain\Seo\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stand-in for real models with pages (products, service categories) in SEO tests.
 *
 * @property int $id
 * @property string $name
 */
class SeoTestPage extends Model implements InSitemap, Seoable
{
    use HasSeo;

    protected $guarded = [];

    public static function createTable(): void
    {
        Schema::create('seo_test_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function seoDefaults(): SeoData
    {
        return new SeoData(title: $this->name, description: "Описание: {$this->name}");
    }

    public function toSitemapTag(): string
    {
        return url("/test-pages/{$this->id}");
    }

    /**
     * @return Builder<self>
     */
    public static function sitemapQuery(): Builder
    {
        return self::query();
    }
}
