<?php

use App\Domain\Seo\Models\SeoMeta;
use App\Domain\Seo\SeoData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('renders title, description, canonical and Open Graph tags', function () {
    $view = $this->blade('<x-seo.meta :seo="$seo" />', ['seo' => new SeoData(
        title: 'Массаж для тела — Body Balance',
        description: 'Описание "в кавычках" <script>',
        canonical: 'https://bodybalance.by/uslugi/massazh-tela',
        image: 'https://bodybalance.by/storage/seo/og.jpg',
    )]);

    $view->assertSee('<title>Массаж для тела — Body Balance</title>', false)
        ->assertSee('<meta name="description" content="Описание &quot;в кавычках&quot; &lt;script&gt;">', false)
        ->assertSee('<link rel="canonical" href="https://bodybalance.by/uslugi/massazh-tela">', false)
        ->assertSee('<meta property="og:image" content="https://bodybalance.by/storage/seo/og.jpg">', false)
        ->assertDontSee('name="robots"', false);
});

it('adds robots noindex only for hidden pages', function () {
    $this->blade('<x-seo.meta :seo="$seo" />', ['seo' => new SeoData(title: 'Корзина', noindex: true)])
        ->assertSee('<meta name="robots" content="noindex, follow">', false);
});

it('resolves SEO of the current route when no data is passed', function () {
    config(['seo.pages.test.page' => ['label' => 'Тест', 'title' => 'Тестовая страница']]);
    SeoMeta::create(['route_name' => 'test.page', 'description' => 'Описание из админки']);
    Route::get('/seo-component-test', fn () => Blade::render('<x-seo.meta />'))->name('test.page');

    $this->get('/seo-component-test?utm_source=x')
        ->assertOk()
        ->assertSee('<title>Тестовая страница — Body Balance</title>', false)
        ->assertSee('Описание из админки')
        ->assertSee('<link rel="canonical" href="http://localhost/seo-component-test">', false);
});
