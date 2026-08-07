<?php

use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\User;
use Database\Seeders\InitialAdminSeeder;

test('it seeds the initial admin user and admin profile', function () {
    config()->set('app.initial_admin', [
        'name' => 'Admin',
        'code' => 'AD000001',
        'last_name' => 'Principal',
        'phone' => '5551234567',
        'email' => 'admin@admin.test',
    ]);

    app(InitialAdminSeeder::class)->run();

    $adminUser = User::query()->where('email', 'admin@admin.test')->first();

    expect($adminUser)->not->toBeNull();
    expect($adminUser->code)->toBe('AD000001');
    expect($adminUser->role)->toBe(UserRole::Admin);
    expect(password_verify('AD000001', $adminUser->password))->toBeTrue();
    expect(Admin::query()->where('user_id', $adminUser->id)->exists())->toBeTrue();
});

test('it does not duplicate the initial admin user when seeded multiple times', function () {
    config()->set('app.initial_admin', [
        'name' => 'Admin',
        'code' => 'AD000001',
        'last_name' => 'Principal',
        'phone' => '5551234567',
        'email' => 'admin@admin.test',
    ]);

    app(InitialAdminSeeder::class)->run();
    app(InitialAdminSeeder::class)->run();

    expect(User::query()->where('email', 'admin@admin.test')->count('*'))->toBe(1);

    $adminUser = User::query()->where('email', 'admin@admin.test')->first();

    expect(Admin::query()->where('user_id', $adminUser->id)->count('*'))->toBe(1);
});
