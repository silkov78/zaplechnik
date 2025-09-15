<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => [
                'required',
                Password::min(8)
                    ->max(255)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Parameter “email” is required. The entered email does not exist',
            'password.required' => 'Parameter “password” is required. Incorrect password entered',
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