<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisitStoreRequest extends FormRequest
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
     * Validations:
     * campground_id - it's possible to pass values like "2", 8.0, "12.0".
     * visit_date
     */
    public function rules(): array
    {
        return [
            'campground_id' => 'required|decimal:0|gt:0',
            'visit_date' => [
                'nullable',
                Rule::date()
                    ->format('Y-m-d')
                    ->todayOrBefore()
                    ->after('1924-01-01'),
            ],
        ];
    }
}
