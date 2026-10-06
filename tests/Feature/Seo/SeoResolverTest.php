<?php

use App\Domain\Seo\Models\SeoMeta;
use App\Domain\Seo\SeoResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixtures\SeoTestPage;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'seo.site_name' => 'Body Balance',
        'seo.title_separator' => ' — ',
        'seo.default_description' => 'Описание по умолчанию',
        'seo.default_image' => null,
        'seo.pages' => [
            'home' => ['label' => 'Главная', 'title' => 'Студия в Копище', 'h1' => 'Body Balance'],
            'about' => ['label' => 'О студии', 'title' => 'О студии Body Balance'],
        ],
    ]);
});

describe('fixed pages', function () {
    it('uses page defaults and appends the site name when the admin left fields empty', function () {
        $seo = app(SeoResolver::class)->forRoute('home');

        expect($seo->title)->toBe('Студия в Копище — Body Balance')
            ->and($seo->description)->toBe('Описание по умолчанию')
            ->and($seo->h1)->toBe('Body Balance')
            ->and($seo->canonical)->toBeNull()
            ->and($seo->noindex)->toBeFalse();
    });

    it('does not repeat the site name if the default title already contains it', function () {
        expect(app(SeoResolver::class)->forRoute('about')->title)->toBe('О студии Body Balance');
    });

    it('uses admin values verbatim', function () {
        SeoMeta::create([
            'route_name' => 'home',
            'title' => 'Массаж в Копище',
            'description' => 'Своё описание',
            'h1' => 'Свой H1',
            'canonical_url' => 'https://bodybalance.by/',
            'noindex' => true,
        ]);

        $seo = app(SeoResolver::class)->forRoute('home');

        expect($seo->title)->toBe('Массаж в Копище')
            ->and($seo->description)->toBe('Своё описание')
            ->and($seo->h1)->toBe('Свой H1')
            ->and($seo->canonical)->toBe('https://bodybalance.by/')
            ->and($seo->noindex)->toBeTrue();
    });

    it('falls back to the site name for pages without config', function () {
        expect(app(SeoResolver::class)->forRoute('unknown.page')->title)->toBe('Body Balance')
            ->and(app(SeoResolver::class)->forRoute(null)->title)->toBe('Body Balance');
    });
});

describe('model pages', function () {
    beforeEach(fn () => SeoTestPage::createTable());

    it('builds SEO from model defaults', function () {
        $page = SeoTestPage::create(['name' => 'Массаж для тела']);

        $seo = app(SeoResolver::class)->forModel($page);

        expect($seo->title)->toBe('Массаж для тела — Body Balance')
            ->and($seo->description)->toBe('Описание: Массаж для тела')
            ->and($seo->h1)->toBe('Массаж для тела');
    });

    it('prefers values saved in the admin panel', function () {
        $page = SeoTestPage::create(['name' => 'Массаж для тела']);
        $page->seo()->create(['title' => 'Массаж тела в Копище — цены', 'h1' => 'Массаж тела']);

        $seo = app(SeoResolver::class)->forModel($page);

        expect($seo->title)->toBe('Массаж тела в Копище — цены')
            ->and($seo->h1)->toBe('Массаж тела')
            ->and($seo->description)->toBe('Описание: Массаж для тела');
    });

    it('deletes SEO meta together with the model', function () {
        $page = SeoTestPage::create(['name' => 'Уходы для лица']);
        $page->seo()->create(['title' => 'Уходы']);

        $page->delete();

        expect(SeoMeta::count())->toBe(0);
    });
});
