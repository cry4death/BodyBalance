<?php

function robotsLines(string $content): array
{
    return array_map('trim', explode("\n", trim($content)));
}

it('closes the whole site outside production', function () {
    $response = $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

    expect(robotsLines($response->getContent()))->toBe(['User-agent: *', 'Disallow: /']);
});

it('opens the site in production, closing only private sections', function () {
    app()->detectEnvironment(fn () => 'production');

    $lines = robotsLines($this->get('/robots.txt')->assertOk()->getContent());

    expect($lines)
        ->not->toContain('Disallow: /')
        ->toContain('Disallow: /admin')
        ->toContain('Disallow: /oformlenie')
        ->toContain('Sitemap: http://localhost/sitemap.xml');
});

it('marks every page noindex outside production', function () {
    $this->get('/')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

it('lets search engines index public pages in production', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->get('/')->assertHeaderMissing('X-Robots-Tag');
});

it('always hides the admin panel from search engines', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->get('/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});
