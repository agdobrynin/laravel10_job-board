<?php

namespace App\Http\Requests;

use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacanciesIndexRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string|min:3',
            'salary_min' => 'nullable|integer|min:1',
            'salary_max' => [
                'nullable',
                'integer',
                Rule::when(fn($attr) => (bool)$attr['salary_min'], 'gte:salary_min'),
            ],
            'experience' => ['nullable', Rule::in(VacancyExperienceEnum::values())],
            'category' => ['nullable', Rule::in(VacancyCategoryEnum::values())],
        ];
    }
}
