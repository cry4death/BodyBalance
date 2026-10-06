<?php

namespace App\Domain\Seo;

/**
 * SEO values for one page. Used both as model/page defaults and as the resolved result.
 */
final readonly class SeoData
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $h1 = null,
        public ?string $canonical = null,
        public ?string $image = null,
        public bool $noindex = false,
    ) {}
}
