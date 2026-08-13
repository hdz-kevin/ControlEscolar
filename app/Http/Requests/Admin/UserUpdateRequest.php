<?php

namespace App\Http\Requests\Admin;

use App\Concerns\ProfileValidationRules;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UserUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            ...$this->profileRules($user->id),
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'specialty' => ['nullable', 'string', 'max:150'],
            'birth_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Add role-aware validation for profile data.
     */
    public function withValidator(Validator $validator): void
    {
        /** @var User $user */
        $user = $this->route('user');

        $validator->sometimes('specialty', ['required', 'string', 'max:150'], function () use ($user): bool {
            return $user->role === UserRole::Teacher;
        });

        $validator->sometimes('birth_date', ['nullable', 'date'], function () use ($user): bool {
            return $user->role === UserRole::Student;
        });
    }
}
