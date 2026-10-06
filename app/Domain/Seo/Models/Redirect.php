<?php

namespace App\Domain\Seo\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Manual 301/302 redirect, applied only when the requested URL would otherwise return 404.
 */
#[Fillable(['from_path', 'to_url', 'status_code'])]
class Redirect extends Model
{
    public const array STATUS_CODES = [
        301 => '301 — навсегда (страница переехала)',
        302 => '302 — временно',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'hits' => 'integer',
            'last_hit_at' => 'datetime',
        ];
    }

    /**
     * Stored as "/path/without/trailing/slash", without query string or domain.
     *
     * @return Attribute<string, string>
     */
    protected function fromPath(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => self::normalizePath($value),
        );
    }

    public static function normalizePath(string $path): string
    {
        $path = parse_url(trim($path), PHP_URL_PATH) ?: '/';

        return '/'.trim(rawurldecode($path), '/');
    }
}
