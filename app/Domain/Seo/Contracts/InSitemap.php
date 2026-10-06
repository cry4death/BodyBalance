<?php

namespace App\Domain\Seo\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Sitemap\Contracts\Sitemapable;

/**
 * A model whose pages go into sitemap.xml. Register the class in config('seo.sitemap.models').
 */
interface InSitemap extends Sitemapable
{
    /**
     * Records to include, e.g. only active products.
     *
     * @return Builder<covariant \Illuminate\Database\Eloquent\Model>
     */
    public static function sitemapQuery(): Builder;
}
