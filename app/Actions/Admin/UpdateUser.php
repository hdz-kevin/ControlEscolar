<?php

namespace App\Actions\Admin;

use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class UpdateUser
{
    /**
     * Update user and profile fields.
     *
     * @param  array{name?:string,email?:string,role?:UserRole|string,last_name?:string|null,phone?:string|null,specialty?:string,birth_date?:string|null}  $attributes
     *
     * @throws Throwable
     */
    public function handle(User $user, array $attributes): User
    {
        if (array_key_exists('role', $attributes)) {
            // TODO: Assume that the role is a UserRole type
            $requestedRole = $this->resolveRole($attributes['role']);

            /**
             * * Important: In the first version, the role cannot be changed.
             */
            if ($requestedRole !== $user->role) {
                throw ValidationException::withMessages([
                    'role' => 'Role changes are not allowed in this version.',
                ]);
            }
        }

        return DB::transaction(function () use ($user, $attributes): User {
            $user->fill([
                'name' => $attributes['name'] ?? $user->name,
                'last_name' => $attributes['last_name'] ?? $user->last_name,
                'phone' => $attributes['phone'] ?? $user->phone,
                'email' => $attributes['email'] ?? $user->email,
            ]);

            $user->save();

            $this->updateRoleProfile($user, $attributes);

            return $user->fresh();
        });
    }

    /**
     * @param  array{name?:string,email?:string,role?:UserRole|string,last_name?:string|null,phone?:string|null,specialty?:string,birth_date?:string|null}  $attributes
     */
    private function updateRoleProfile(User $user, array $attributes): void
    {
        if ($user->role === UserRole::Admin) {
            Admin::query()->firstOrCreate([
                'user_id' => $user->id,
            ]);

            return;
        }

        if ($user->role === UserRole::Teacher) {
            $profile = Teacher::query()->firstWhere('user_id', $user->id);

            if ($profile === null) {
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

            if (array_key_exists('specialty', $attributes)) {
                $specialty = $attributes['specialty'];

                if ($specialty === null || trim($specialty) === '') {
                    throw ValidationException::withMessages([
                        'specialty' => 'Specialty is required for teacher users.',
                    ]);
                }

                $profile->update([
                    'specialty' => $specialty,
                ]);
            }

            return;
        }

        $profile = Student::query()->firstWhere('user_id', $user->id);

        if ($profile === null) {
            Student::query()->create([
                'user_id' => $user->id,
                'birth_date' => $attributes['birth_date'] ?? null,
            ]);

            return;
        }

        if (array_key_exists('birth_date', $attributes)) {
            $profile->update([
                'birth_date' => $attributes['birth_date'],
            ]);
        }
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
}
