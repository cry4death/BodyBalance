<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->seed(RoleSeeder::class));

it('blocks customers from the admin panel', function () {
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
});

it('lets staff roles into the admin panel', function (string $role) {
    $this->actingAs(User::factory()->create()->assignRole($role))->get('/admin')->assertOk();
})->with(['owner', 'manager', 'editor']);

it('redirects guests to the admin login', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});
