<?php

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

it('allows admin to update a teacher user through the controller', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $teacher = User::query()->create([
        'name' => 'Maria',
        'last_name' => 'Lopez',
        'email' => 'maria.teacher@example.test',
        'phone' => '5550000000',
        'role' => UserRole::Teacher,
        'code' => 'AA000001',
        'password' => 'AA000001',
        'email_verified_at' => now(),
    ]);

    Teacher::query()->create([
        'user_id' => $teacher->id,
        'specialty' => 'Physics',
    ]);

    $response = $this->actingAs($admin)->put(route('users.update', $teacher), [
        'name' => 'Maria Elena',
        'email' => 'maria.elena.teacher@example.test',
        'specialty' => 'Computer Science',
    ]);

    $response->assertRedirect(route('users.edit', $teacher, absolute: false));

    $teacher->refresh();

    expect($teacher->name)->toBe('Maria Elena');
    expect($teacher->email)->toBe('maria.elena.teacher@example.test');
    expect($teacher->role)->toBe(UserRole::Teacher);
    expect(Teacher::query()->where('user_id', $teacher->id)->value('specialty'))->toBe('Computer Science');
});

it('allows admin to delete a user through the controller', function () {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $student = User::query()->create([
        'name' => 'Carlos',
        'last_name' => 'Diaz',
        'email' => 'carlos.student@example.test',
        'phone' => null,
        'role' => UserRole::Student,
        'code' => 'AB000002',
        'password' => 'AB000002',
        'email_verified_at' => now(),
    ]);

    Student::query()->create([
        'user_id' => $student->id,
        'birth_date' => null,
    ]);

    $response = $this->actingAs($admin)->delete(route('users.destroy', $student));

    $response->assertRedirect(route('users.index', absolute: false));

    expect(User::query()->find($student->id))->toBeNull();
    expect(Student::query()->where('user_id', $student->id)->exists())->toBeFalse();
});
