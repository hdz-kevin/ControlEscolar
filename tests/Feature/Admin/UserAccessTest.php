<?php

use App\Enums\UserRole;
use App\Models\Teacher;
use App\Models\User;

test('guests are redirected to login for users store', function () {
    $response = $this->post(route('users.store'));

    $response->assertRedirect(route('login'));
});

test('admin can create a user through the store endpoint', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Ana',
        'last_name' => 'Torres',
        'email' => 'ana.teacher@example.test',
        'phone' => '5512345678',
        'role' => UserRole::Teacher->value,
        'specialty' => 'Mathematics',
    ]);

    $user = User::query()->where('email', 'ana.teacher@example.test')->first();

    $response->assertRedirect(route('users.edit', $user, absolute: false));

    expect($user)->not->toBeNull();
    expect(Teacher::query()->where('user_id', $user->id)->exists())->toBeTrue();
});

it('denies users store access to non admins', function (UserRole $role) {
    $user = User::factory()->create([
        'role' => $role,
    ]);

    $this->actingAs($user)
        ->post(route('users.store'))
        ->assertForbidden();
})->with([
    UserRole::Teacher,
    UserRole::Student,
]);
