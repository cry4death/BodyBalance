<?php

namespace App\Domain\Seo\Contracts;

use App\Domain\Seo\Models\SeoMeta;
use App\Domain\Seo\SeoData;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * A model with its own page (product, service category, brand...).
 * Implement together with the HasSeo trait.
 */
interface Seoable
{
    /**
     * @return MorphOne<SeoMeta, covariant \Illuminate\Database\Eloquent\Model>
     */
    public function seo(): MorphOne;

    /**
     * Fallback values used when the admin left a field empty, e.g. title = product name + brand.
     */
    public function seoDefaults(): SeoData;
}
