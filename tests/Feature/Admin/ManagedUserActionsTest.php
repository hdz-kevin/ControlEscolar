<?php

use App\Actions\Admin\CreateUser;
use App\Actions\Admin\DeleteUser;
use App\Actions\Admin\UpdateUser;
use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('it creates a teacher user with a generated code and teacher profile', function () {
    $user = app(CreateUser::class)->handle([
        'name' => 'Ana',
        'last_name' => 'Torres',
        'email' => 'ana.teacher@example.test',
        'phone' => '5512345678',
        'role' => UserRole::Teacher,
        'specialty' => 'Mathematics',
    ]);

    expect($user->role)->toBe(UserRole::Teacher);
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->code)->toMatch('/^[A-Z0-9]{8}$/');
    expect(password_verify($user->code, $user->password))->toBeTrue();

    expect(Teacher::query()->where('user_id', $user->id)->exists())->toBeTrue();
    expect(Student::query()->where('user_id', $user->id)->exists())->toBeFalse();
    expect(Admin::query()->where('user_id', $user->id)->exists())->toBeFalse();
});

test('it creates a student user and allows nullable birth date', function () {
    $user = app(CreateUser::class)->handle([
        'name' => 'Luis',
        'last_name' => 'Garcia',
        'email' => 'luis.student@example.test',
        'role' => UserRole::Student,
    ]);

    expect($user->role)->toBe(UserRole::Student);

    $profile = Student::query()->firstWhere('user_id', $user->id);

    expect($profile)->not->toBeNull();
    expect($profile->birth_date)->toBeNull();
});

test('it updates base fields and teacher specialty without changing role', function () {
    $user = User::query()->create([
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
        'user_id' => $user->id,
        'specialty' => 'Physics',
    ]);

    $updated = app(UpdateUser::class)->handle($user, [
        'name' => 'Maria Elena',
        'email' => 'maria.elena.teacher@example.test',
        'specialty' => 'Computer Science',
    ]);

    expect($updated->name)->toBe('Maria Elena');
    expect($updated->email)->toBe('maria.elena.teacher@example.test');
    expect($updated->role)->toBe(UserRole::Teacher);

    $profile = Teacher::query()->firstWhere('user_id', $user->id);
    expect($profile->specialty)->toBe('Computer Science');
});

test('it rejects role changes during update', function () {
    $user = User::query()->create([
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
        'user_id' => $user->id,
        'birth_date' => null,
    ]);

    expect(fn () => app(UpdateUser::class)->handle($user, [
        'role' => UserRole::Teacher,
        'specialty' => 'History',
    ]))->toThrow(ValidationException::class);

    expect($user->fresh()->role)->toBe(UserRole::Student);
});

test('it deletes user and cascades profile rows', function () {
    $user = User::query()->create([
        'name' => 'Rocio',
        'last_name' => 'Nava',
        'email' => 'rocio.teacher@example.test',
        'phone' => '5551112222',
        'role' => UserRole::Teacher,
        'code' => 'AC000003',
        'password' => 'AC000003',
        'email_verified_at' => now(),
    ]);

    Teacher::query()->create([
        'user_id' => $user->id,
        'specialty' => 'Biology',
    ]);

    app(DeleteUser::class)->handle($user);

    expect(User::query()->find($user->id))->toBeNull();
    expect(Teacher::query()->where('user_id', $user->id)->exists())->toBeFalse();
});
