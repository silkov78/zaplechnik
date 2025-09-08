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

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'message' => 'Invalid request',
            'errors' => $validator->errors()
        ], 400));
    }
}
