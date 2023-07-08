<?php

namespace Database\Factories;

use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vacancy>
 */
class VacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle,
            'description' => fake()->paragraphs(rand(3, 6), true),
            'salary' => fake()->numberBetween(5_000, 10_000),
            'location' => fake()->city,
            'category' => fake()->randomElement(VacancyCategoryEnum::values()),
            'experience' => fake()->randomElement(VacancyExperienceEnum::values()),
        ];
    }
}
