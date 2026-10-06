<?php

namespace App\Domain\Seo\Concerns;

use App\Domain\Seo\Models\SeoMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin Model
 */
trait HasSeo
{
    public static function bootHasSeo(): void
    {
        static::deleted(function (self $model): void {
            $model->seo()->delete();
        });
    }

    /**
     * @return MorphOne<SeoMeta, $this>
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
