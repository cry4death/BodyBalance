<?php

use App\Domain\Seo\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects a missing old URL and counts the hit', function () {
    $redirect = Redirect::create(['from_path' => '/old-page', 'to_url' => '/new-page', 'status_code' => 301]);

    $this->get('/old-page')->assertRedirect('/new-page')->assertStatus(301);

    expect($redirect->fresh())
        ->hits->toBe(1)
        ->last_hit_at->not->toBeNull();
});

it('keeps the query string, e.g. utm tags', function () {
    Redirect::create(['from_path' => '/old-page', 'to_url' => '/new-page', 'status_code' => 302]);

    $this->get('/old-page?utm_source=instagram')
        ->assertStatus(302)
        ->assertRedirect('/new-page?utm_source=instagram');
});

it('normalizes pasted full URLs and trailing slashes', function () {
    $redirect = Redirect::create(['from_path' => 'https://bodybalance.by/Old-Page/?x=1', 'to_url' => '/new', 'status_code' => 301]);

    expect($redirect->from_path)->toBe('/Old-Page');
    $this->get('/Old-Page/')->assertRedirect('/new');
});

it('works with Cyrillic addresses', function () {
    Redirect::create(['from_path' => '/старая-страница', 'to_url' => '/novaya', 'status_code' => 301]);

    $this->get('/'.rawurlencode('старая-страница'))->assertRedirect('/novaya');
});

it('never overrides an existing page', function () {
    Redirect::create(['from_path' => '/', 'to_url' => '/elsewhere', 'status_code' => 301]);

    $this->get('/')->assertOk();
});

it('returns 404 when there is no redirect', function () {
    $this->get('/no-such-page')->assertNotFound();
});

it('does not redirect form submissions', function () {
    Redirect::create(['from_path' => '/old-form', 'to_url' => '/new-form', 'status_code' => 301]);

    $this->post('/old-form')->assertNotFound();
});
