<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteUser
{
    /**
     * Delete a user account.
     *
     * @throws Throwable
     */
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->delete();
        });
    }
}
