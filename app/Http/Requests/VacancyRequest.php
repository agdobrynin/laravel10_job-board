<?php

namespace App\Http\Requests;

use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use App\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()
            ->can('create', Vacancy::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:10',
            'description' => 'required|string|min:50',
            'salary' => 'required|integer|min:1',
            'location' => 'required|string|min:5',
            'experience' => [
                'required',
                Rule::in(VacancyExperienceEnum::values()),
            ],
            'category' => [
                'required',
                Rule::in(VacancyCategoryEnum::values()),
            ],
        ];
    }
}
