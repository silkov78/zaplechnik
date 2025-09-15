<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:users|max:50',
            'email' => 'required|unique:users|email|max:255',
            'password' => [
                'required',
                Password::min(8)
                    ->max(255)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name' => 'Parameter “name” is required and unique. It must be a string and less than 50 characters',
            'email' => 'Parameter “email” is required and unique. It must be a string and less than 255 characters',
            'password' => 'Parameter “password” is required. ' .
                          'It must be a string of more than 8 characters, but no more than 255 characters. ' .
                          'It must contain uppercase and lowercase letters of the Latin alphabet and at least one digit',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'message' => 'Invalid request',
            'errors' => $validator->errors()
        ], 400));
    }
}
