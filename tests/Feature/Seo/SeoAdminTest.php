<?php

use App\Domain\Seo\Models\Redirect;
use App\Domain\Seo\Models\SeoMeta;
use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\SeoPages\Pages\CreateSeoPage;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->actingAs(User::factory()->create()->assignRole('owner'));
});

it('opens the SEO sections of the admin panel', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/admin/seo-pages', '/admin/seo-pages/create', '/admin/redirects', '/admin/redirects/create']);

it('saves SEO of a fixed page', function () {
    Livewire::test(CreateSeoPage::class)
        ->fillForm(['route_name' => 'home', 'title' => 'Массаж в Копище', 'noindex' => false])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(SeoMeta::where('route_name', 'home')->value('title'))->toBe('Массаж в Копище');
});

it('does not allow two SEO records for one page', function () {
    SeoMeta::create(['route_name' => 'home']);

    Livewire::test(CreateSeoPage::class)
        ->fillForm(['route_name' => 'home'])
        ->call('create')
        ->assertHasFormErrors(['route_name' => 'unique']);
});

it('creates a redirect with a normalized old address', function () {
    Livewire::test(CreateRedirect::class)
        ->fillForm(['from_path' => 'https://bodybalance.by/old/', 'to_url' => '/new', 'status_code' => 301])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Redirect::sole()->from_path)->toBe('/old');
});

it('rejects a redirect to the same address', function () {
    Livewire::test(CreateRedirect::class)
        ->fillForm(['from_path' => '/same', 'to_url' => '/same/', 'status_code' => 301])
        ->call('create')
        ->assertHasFormErrors(['to_url']);
});
