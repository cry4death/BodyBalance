<?php

use App\Domain\Seo\Models\SeoMeta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixtures\SeoTestPage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->sitemapPath = storage_path('framework/testing/sitemap.xml');
    @mkdir(dirname($this->sitemapPath), recursive: true);
    @unlink($this->sitemapPath);

    SeoTestPage::createTable();

    config([
        'seo.sitemap.path' => $this->sitemapPath,
        'seo.sitemap.routes' => ['home', 'route.that.does.not.exist'],
        'seo.sitemap.models' => [SeoTestPage::class],
    ]);
});

afterEach(fn () => @unlink($this->sitemapPath));

it('writes fixed pages and model pages', function () {
    $page = SeoTestPage::create(['name' => 'Массаж для тела']);

    $this->artisan('seo:sitemap')->assertSuccessful();

    expect(file_get_contents($this->sitemapPath))
        ->toContain('<loc>http://localhost</loc>')
        ->toContain("<loc>http://localhost/test-pages/{$page->id}</loc>");
});

it('skips pages hidden from search engines', function () {
    SeoMeta::create(['route_name' => 'home', 'noindex' => true]);
    $hidden = SeoTestPage::create(['name' => 'Скрытая']);
    $hidden->seo()->create(['noindex' => true]);
    $visible = SeoTestPage::create(['name' => 'Видимая']);

    $this->artisan('seo:sitemap')->assertSuccessful();

    expect(file_get_contents($this->sitemapPath))
        ->not->toContain('<loc>http://localhost</loc>')
        ->not->toContain("/test-pages/{$hidden->id}<")
        ->toContain("/test-pages/{$visible->id}<");
});
