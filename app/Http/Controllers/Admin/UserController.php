<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('users/Index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('users/Create');
    }

    /**
     * Store a newly created user.
     */
    public function store(): Response
    {
        abort(Response::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): InertiaResponse
    {
        return Inertia::render('users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(User $user): Response
    {
        abort(Response::HTTP_NOT_IMPLEMENTED);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): Response
    {
        abort(Response::HTTP_NOT_IMPLEMENTED);
    }
}
