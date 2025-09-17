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
            'avatar' => 'file|image|mimes:jpeg,png,jpg,bmp,gif|dimensions:ratio=1/1|max:2048',
            'gender' => [new Enum(Gender::class)],
            'bio' => 'string|max:255',
            'telegram' => 'string|starts_with:@|max:100',
            'is_private' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name' => 'Parameter “name” is unique. It must be a string and less than 50 characters',
            'email' => 'Parameter “email” is unique. It must be a string and less than 255 characters',
            'avatar' => 'Parameter “avatar” must be a square and less than 2 megabytes in size. ' .
                        'Acceptable format is JPEG, JPG, GIF, PNG и BMP.',
            'gender' => 'Parameter “gender” must be female or male.',
            'bio' => 'Parameter “bio”  must be less than 255 characters.',
            'telegram' => 'Parameter “telegram”  must must start with the character “@” and less than 100 characters.',
            'is_private' => 'Parameter “is_private” must be boolean.',
        ];
    }
}
