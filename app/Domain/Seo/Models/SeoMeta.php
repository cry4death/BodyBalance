<?php

namespace App\Domain\Seo\Models;

use App\Domain\Seo\SeoData;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * SEO fields of a model page (seoable) or of a fixed site page (route_name).
 */
#[Fillable(['route_name', 'title', 'description', 'h1', 'canonical_url', 'og_image', 'noindex'])]
class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'noindex' => 'boolean',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function pageLabel(): string
    {
        return config("seo.pages.{$this->route_name}.label") ?? (string) $this->route_name;
    }

    public function toSeoData(): SeoData
    {
        return new SeoData(
            title: $this->title,
            description: $this->description,
            h1: $this->h1,
            canonical: $this->canonical_url,
            image: $this->og_image ? Storage::disk('public')->url($this->og_image) : null,
            noindex: $this->noindex,
        );
    }
}
