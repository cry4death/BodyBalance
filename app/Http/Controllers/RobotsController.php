<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * robots.txt: closed everywhere except production, so staging never gets indexed.
 */
class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = app()->isProduction()
            ? [
                'User-agent: *',
                ...array_map(fn (string $path): string => "Disallow: {$path}", config('seo.robots.disallow')),
                ...config('seo.robots.extra'),
                '',
                'Sitemap: '.url('sitemap.xml'),
            ]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
