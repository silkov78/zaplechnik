<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['sometimes', 'string', 'max:50', 'min:1', Rule::unique('users')->ignore($user)],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            // TODO: add dimensions validation when front will be ready
            'avatar' => 'sometimes|file|image|mimes:jpeg,png,jpg,bmp,gif|max:2048',
            'gender' => ['sometimes', Rule::in(Gender::values())],
            'bio' => 'sometimes|string|max:255',
            'telegram' => 'sometimes|string|starts_with:@|max:100',
            'is_private' => 'sometimes|boolean',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (empty($this->validated())) {
                $validator->errors()->add(
                    'fields', 'At least one field must be provided.'
                );
            }
        });
    }
}
