<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterStoreRequest extends FormRequest
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
        $passwordRule = (new Password(8));

        if(App::isProduction()) {
            $passwordRule->letters()
                ->numbers()
                ->mixedCase()
                ->symbols();
        }

        return [
            'email' => 'required|email:rfc,strict|unique:App\Models\User,email',
            'name' => 'required|string|min:3',
            'password' => ['required', 'string', $passwordRule, 'confirmed'],
            'is_employer' => 'nullable|boolean',
            'employer_name' => [
                'nullable',
                'required_with:is_employer',
                Rule::when(true, [
                    'string',
                    'min:5',
                    'unique:App\Models\Employer,name',
                ]),
            ],
        ];
    }
}
