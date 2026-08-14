<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateUser;
use App\Actions\Admin\DeleteUser;
use App\Actions\Admin\UpdateUser;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly CreateUser $createUser,
        private readonly UpdateUser $updateUser,
        private readonly DeleteUser $deleteUser,
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('users/Index', [
            'users' => User::query()
                ->select(['id', 'name', 'last_name', 'email', 'phone', 'role', 'code', 'created_at'])
                ->latest('id')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('users/Create', [
            'roles' => $this->rolesOptions(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $user = $this->createUser->handle($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User created.'),
        ]);

        return to_route('users.edit', $user);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): InertiaResponse
    {
        $user->loadMissing(['teacherProfile', 'studentProfile']);

        return Inertia::render('users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role->value,
                'code' => $user->code,
                'specialty' => $user->teacherProfile?->specialty,
                'birth_date' => $user->studentProfile?->birth_date?->toDateString(),
            ],
            'roles' => $this->rolesOptions(),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->updateUser->handle($user, $request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User updated.'),
        ]);

        return to_route('users.edit', $user);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->deleteUser->handle($user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User deleted.'),
        ]);

        return to_route('users.index');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function rolesOptions(): array
    {
        return array_map(
            fn (UserRole $role): array => [
                'value' => $role->value,
                'label' => Str::headline($role->value),
            ],
            UserRole::cases(),
        );
    }
}
