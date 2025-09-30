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
            'name' => ['string', 'max:50', 'min:1', Rule::unique('users')->ignore($user)],
            'email' => ['string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'avatar' => 'file|image|mimes:jpeg,png,jpg,bmp,gif|dimensions:ratio=1/1|max:2048',
            'gender' => Rule::in(Gender::values()),
            'bio' => 'string|max:255',
            'telegram' => 'string|starts_with:@|max:100',
            'is_private' => 'boolean',
        ];
    }
}
