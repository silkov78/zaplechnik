<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProfileUpdateRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|unique:users|max:50',
            'email' => 'email|unique:users|max:255',
            'avatar' => 'file|image|mimes:jpeg,png,jpg,bmp,gif|max:2048',
            'gender' => [new Enum(Gender::class)],
            'bio' => 'string|max:255',
            'telegram' => 'string|starts_with:@|max:100',
        ];
    }
}
