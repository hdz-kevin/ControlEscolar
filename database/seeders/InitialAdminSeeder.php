<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class InitialAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = config('app.initial_admin');

        $user = User::query()->firstOrCreate(
            ['email' => $admin['email']],
            $admin + [
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ]
        );

        Admin::query()->firstOrCreate(['user_id' => $user->id]);
    }
}
