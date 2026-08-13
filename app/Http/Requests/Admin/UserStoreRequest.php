<?php

namespace App\Http\Requests\Admin;

use App\Concerns\ProfileValidationRules;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UserStoreRequest extends FormRequest
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
        return [
            ...$this->profileRules(),
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in(UserRole::values())],
            'specialty' => ['nullable', 'string', 'max:150'],
            'birth_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Get the validation rules that depend on the selected role.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    public function withValidator(Validator $validator): void
    {
        $validator->sometimes('specialty', ['required', 'string', 'max:150'], function (): bool {
            return $this->input('role') === UserRole::Teacher->value;
        });
    }
}
