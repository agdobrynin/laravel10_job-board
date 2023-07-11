<?php

namespace Tests\Unit;

use App\Dto\FilterVacancyDto;
use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use PHPUnit\Framework\TestCase;

class FilterVacancyDtoTest extends TestCase
{
    /** @dataProvider data */
    public function test_dto(array $data, FilterVacancyDto $res): void
    {
        $dto = new FilterVacancyDto(...$data);
        $this->assertEquals($dto, $res);
    }

    public static function data(): \Generator
    {
        yield '#1' => [
            [
                'experience' => '',
                'category' => '',
                'salary_min' => 1_000,
                'salary_max' => 10_000,
                'search' => 'abc',
            ],
            new FilterVacancyDto(search: 'abc', salary_min: 1000, salary_max: 10000, experience: null, category: null)
        ];

        yield '#2' => [
            [
                'experience' => null,
                'category' => null,
            ],
            new FilterVacancyDto()
        ];

        foreach (VacancyExperienceEnum::cases() as $index => $case) {
            yield '#3.' . $index => [
                ['experience' => $case->value],
                new FilterVacancyDto(experience: $case)
            ];
        }

        yield '#4' => [
            ['experience' => 'wrong_val'],
            new FilterVacancyDto(experience: null)
        ];

        foreach (VacancyCategoryEnum::cases() as $index => $case) {
            yield '#5.' . $index => [
                ['category' => $case->value],
                new FilterVacancyDto(category: $case)
            ];
        }

        yield '#6' => [
            ['category' => 'wrong_val', 'search' => 'popop'],
            new FilterVacancyDto(search: 'popop', category: null)
        ];
    }
}
