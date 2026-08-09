<?php

use App\Enums\UserRole;
use App\Models\User;

test('guests are redirected to login for users store', function () {
    $response = $this->post(route('users.store'));

    $response->assertRedirect(route('login'));
});

test('admin can access users store endpoint', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->post(route('users.store'))
        ->assertStatus(501);
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
