<?php
declare(strict_types=1);

namespace App\Dto;

use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;

readonly class FilterVacancyDto
{
    public ?VacancyExperienceEnum $experience;
    public ?VacancyCategoryEnum $category;

    public function __construct(
        public ?string               $search = null,
        public ?int                  $salary_min = null,
        public ?int                  $salary_max = null,
        string|VacancyExperienceEnum $experience = null,
        string|VacancyCategoryEnum   $category = null,
    )
    {
        $experience instanceof VacancyExperienceEnum
            ? $this->experience = $experience
            : $this->experience = VacancyExperienceEnum::make($experience ?: '');

        $category instanceof VacancyCategoryEnum
            ? $this->category = $category
            : $this->category = VacancyCategoryEnum::make($category ?: '');
    }
}
