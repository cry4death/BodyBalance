<?php

namespace App\Domain\Studio\Settings;

use Illuminate\Support\Facades\Storage;
use Spatie\LaravelSettings\Settings;

/**
 * One-off texts and photos of the home page: hero and «О студии».
 * Repeating blocks (services, specialists, reviews, advantages, FAQ) are separate models.
 */
class HomePageSettings extends Settings
{
    public string $hero_title;

    public string $hero_slogan;

    /** Path on the public disk. */
    public ?string $hero_image;

    public ?string $hero_image_alt;

    public string $about_title;

    /** Plain text, paragraphs separated by an empty line. */
    public ?string $about_text;

    /**
     * Rows like ['image' => 'home/gallery/1.jpg', 'alt' => 'Кабинет массажа'].
     *
     * @phpstan-var list<array{image: string, alt: string}>
     */
    public array $about_gallery;

    public static function group(): string
    {
        return 'home';
    }

    /**
     * @return list<string>
     */
    public function aboutParagraphs(): array
    {
        $paragraphs = preg_split('/\R\s*\R/u', trim((string) $this->about_text)) ?: [];

        return array_values(array_filter(array_map('trim', $paragraphs)));
    }

    public function heroImageUrl(): ?string
    {
        return $this->hero_image ? Storage::disk('public')->url($this->hero_image) : null;
    }
}
