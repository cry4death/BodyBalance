<?php

namespace App\Domain\Seo;

use App\Domain\Seo\Contracts\Seoable;
use App\Domain\Seo\Models\SeoMeta;

/**
 * Builds the final SEO for a page: admin values → page/model defaults → site defaults.
 */
final class SeoResolver
{
    public function forRoute(?string $routeName): SeoData
    {
        $meta = $routeName ? SeoMeta::query()->where('route_name', $routeName)->first() : null;

        $defaults = new SeoData(
            title: config("seo.pages.{$routeName}.title"),
            h1: config("seo.pages.{$routeName}.h1"),
        );

        return $this->resolve($meta, $defaults);
    }

    public function forModel(Seoable $model): SeoData
    {
        return $this->resolve($model->seo()->first(), $model->seoDefaults());
    }

    private function resolve(?SeoMeta $meta, SeoData $defaults): SeoData
    {
        $stored = $meta?->toSeoData() ?? new SeoData;
        $siteName = (string) config('seo.site_name');

        // Admin titles are used verbatim; default titles get the site name appended.
        $title = $stored->title
            ?? ($defaults->title ? $this->withSiteName($defaults->title, $siteName) : $siteName);

        return new SeoData(
            title: $title,
            description: $stored->description ?? $defaults->description ?? config('seo.default_description'),
            h1: $stored->h1 ?? $defaults->h1 ?? $defaults->title,
            canonical: $stored->canonical ?? $defaults->canonical,
            image: $stored->image ?? $defaults->image ?? config('seo.default_image'),
            noindex: $stored->noindex || $defaults->noindex,
        );
    }

    private function withSiteName(string $title, string $siteName): string
    {
        return str_contains($title, $siteName)
            ? $title
            : $title.config('seo.title_separator').$siteName;
    }
}
