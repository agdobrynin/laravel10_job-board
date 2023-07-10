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
        public ?string $search = null,
        public ?int    $salary_min = null,
        public ?int    $salary_max = null,
        ?string        $experience = null,
        ?string        $category = null,
    )
    {
        $this->experience = VacancyExperienceEnum::make($experience ?: '');
        $this->category = VacancyCategoryEnum::make($category ?: '');
    }
}
