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
        /** @var VacancyCategoryEnum $category */
        $category = fake()->randomElement(VacancyCategoryEnum::cases());
        /** @var VacancyExperienceEnum $experience */
        $experience = fake()->randomElement(VacancyExperienceEnum::cases());

        $salaryMin = 3_000;
        $salaryMax = 7_000;

        if ($category === VacancyCategoryEnum::IT) {
            $salaryMin = match ($experience) {
                VacancyExperienceEnum::JUNIOR => 4_500,
                VacancyExperienceEnum::MIDDLE => 7_000,
                VacancyExperienceEnum::SENIOR => 10_000,
            };

            $salaryMax = $salaryMin + rand(1_000, 7_000);
        }

        $createdAt = fake()->dateTimeBetween('-2 months');

        return [
            'title' => fake()->jobTitle,
            'description' => fake()->paragraphs(rand(3, 6), true),
            'salary' => fake()->numberBetween($salaryMin, $salaryMax),
            'location' => fake()->city,
            'category' => $category->value,
            'experience' => $experience->value,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
