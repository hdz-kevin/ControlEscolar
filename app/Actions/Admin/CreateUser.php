<?php

namespace App\Actions\Admin;

use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateUser
{
    /**
     * Create a user and its role profile.
     *
     * @param  array{name:string,email:string,role:UserRole|string,last_name?:string|null,phone?:string|null,specialty?:string,birth_date?:string|null}  $attributes
     *
     * @throws Throwable
     */
    public function handle(array $attributes): User
    {
        // TODO: Assume that the role is a UserRole type
        $role = $this->resolveRole($attributes['role'] ?? null);
        $generatedCode = $this->generateUniqueCode();

        return DB::transaction(function () use ($attributes, $role, $generatedCode): User {
            $user = User::query()->create([
                'name' => $attributes['name'],
                'last_name' => $attributes['last_name'] ?? null,
                'phone' => $attributes['phone'] ?? null,
                'email' => $attributes['email'],
                'role' => $role,
                'code' => $generatedCode,
                'password' => $generatedCode,
            ]);

            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();

            $this->createRoleProfile($user, $role, $attributes);

            return $user->fresh();
        });
    }

    /**
     * @param  array{name:string,email:string,role:UserRole|string,last_name?:string|null,phone?:string|null,specialty?:string,birth_date?:string|null}  $attributes
     */
    private function createRoleProfile(User $user, UserRole $role, array $attributes): void
    {
        if ($role === UserRole::Admin) {
            Admin::query()->create([
                'user_id' => $user->id,
            ]);

            return;
        }

        if ($role === UserRole::Teacher) {
            $specialty = $attributes['specialty'] ?? null;

            if ($specialty === null || trim($specialty) === '') {
                throw ValidationException::withMessages([
                    'specialty' => 'Specialty is required for teacher users.',
                ]);
            }

            Teacher::query()->create([
                'user_id' => $user->id,
                'specialty' => $specialty,
            ]);

            return;
        }

        Student::query()->create([
            'user_id' => $user->id,
            'birth_date' => $attributes['birth_date'] ?? null,
        ]);
    }

    private function resolveRole(mixed $role): UserRole
    {
        if ($role instanceof UserRole) {
            return $role;
        }

        if (! is_string($role) || trim($role) === '') {
            throw ValidationException::withMessages([
                'role' => 'Role is required.',
            ]);
        }

        return UserRole::from($role);
    }

    private function generateUniqueCode(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $code = '';

            for ($index = 0; $index < 8; $index++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $code = Str::upper($code);
        } while (User::query()->where('code', $code)->exists());

        return $code;
    }
}
