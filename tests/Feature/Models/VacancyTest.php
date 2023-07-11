<?php

namespace Tests\Feature\Models;

use App\Dto\FilterVacancyDto;
use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_filter_by_title(): void
    {
        Vacancy::factory(3)
            ->sequence(
                ['title' => 'my first vacancy'],
                ['title' => 'my second vacancy'],
                ['title' => 'my thyroid vacancy'],
            )
            ->create();

        $dto = new FilterVacancyDto(search: 'second vacancy');

        $vacancies = Vacancy::filter($dto)->get();

        $this->assertCount(1, $vacancies);
        $this->assertEquals('my second vacancy', $vacancies->first()->title);
    }

    public function test_scope_filter_by_title_and_description(): void
    {
        Vacancy::factory(3)
            ->sequence(
                ['title' => 'my first vacancy'],
                ['title' => 'my second vacancy'],
                ['title' => 'my thyroid vacancy', 'description' => 'This is description for second vacancy here displayed.'],
            )
            ->create();

        $dto = new FilterVacancyDto(search: 'second vacancy');
        /** @var Collection $vacancies */
        $vacancies = Vacancy::filter($dto)->get();

        $this->assertCount(2, $vacancies);
        $this->assertEquals('my second vacancy', $vacancies->getIterator()->offsetGet(0)->title);
        $this->assertEquals('my thyroid vacancy', $vacancies->getIterator()->offsetGet(1)->title);
    }

    public function test_scope_filter_by_title_and_description_and_experience(): void
    {
        Vacancy::factory(3)
            ->sequence(
                [
                    'title' => 'my first vacancy',
                    'experience' => VacancyExperienceEnum::JUNIOR->value
                ],
                [
                    'title' => 'my second vacancy',
                    'experience' => VacancyExperienceEnum::JUNIOR->value
                ],
                [
                    'title' => 'my thyroid vacancy',
                    'experience' => VacancyExperienceEnum::SENIOR->value,
                    'description' => 'This is description for second vacancy here displayed.'
                ],
            )
            ->create();

        $dto = new FilterVacancyDto(search: 'second vacancy', experience: VacancyExperienceEnum::SENIOR);
        /** @var Collection $vacancies */
        $vacancies = Vacancy::filter($dto)->get();

        $this->assertCount(1, $vacancies);
        $this->assertEquals('my thyroid vacancy', $vacancies->getIterator()->offsetGet(0)->title);
        $this->assertEquals(VacancyExperienceEnum::SENIOR->value, $vacancies->getIterator()->offsetGet(0)->experience);
    }

    public function test_scope_filter_by_title_and_description_and_experience_and_salary(): void
    {
        Vacancy::factory(4)
            ->sequence(
                [
                    'title' => 'my first vacancy',
                    'experience' => VacancyExperienceEnum::JUNIOR->value,
                    'salary' => 1_000,
                ],
                [
                    'title' => 'Web designer at Facebook',
                    'experience' => VacancyExperienceEnum::JUNIOR->value,
                    'salary' => 1_000,
                ],
                [
                    'title' => 'Web designer at Google',
                    'experience' => VacancyExperienceEnum::SENIOR->value,
                    'description' => 'The vacancy of Web designer is very interesting.',
                    'salary' => 1_500,
                ]
                ,
                [
                    'title' => 'Web designer and Front-end developer',
                    'experience' => VacancyExperienceEnum::MIDDLE->value,
                    'description' => 'Working as Web designer and front developer easy.',
                    'salary' => 1_500,
                ],
            )
            ->create();

        $dto = new FilterVacancyDto(
            search: 'web designer',
            salary_min: 1_200,
            salary_max: 2_000,
            experience: VacancyExperienceEnum::MIDDLE,
        );
        /** @var Collection $vacancies */
        $vacancies = Vacancy::filter($dto)->get();

        $this->assertCount(1, $vacancies);
        $this->assertEquals('Web designer and Front-end developer', $vacancies->getIterator()->offsetGet(0)->title);
        $this->assertEquals(VacancyExperienceEnum::MIDDLE->value, $vacancies->getIterator()->offsetGet(0)->experience);
    }

    public function test_scope_filter_by_title_and_description_and_experience_and_category_and_salary(): void
    {
        Vacancy::factory(4)
            ->sequence(
                [
                    'title' => 'my first vacancy',
                    'experience' => VacancyExperienceEnum::JUNIOR->value,
                    'salary' => 1_000,
                    'category' => VacancyCategoryEnum::IT->value,
                ],
                [
                    'title' => 'Web designer at Facebook',
                    'experience' => VacancyExperienceEnum::JUNIOR->value,
                    'salary' => 1_000,
                    'category' => VacancyCategoryEnum::IT->value,
                ],
                [
                    'title' => 'Web designer at Google',
                    'experience' => VacancyExperienceEnum::MIDDLE->value,
                    'description' => 'The vacancy of Web designer is very interesting.',
                    'salary' => 1_500,
                    'category' => VacancyCategoryEnum::IT->value,
                ]
                ,
                [
                    'title' => 'Web designer and Front-end developer',
                    'experience' => VacancyExperienceEnum::MIDDLE->value,
                    'description' => 'Working as Web designer and front developer easy.',
                    'salary' => 1_500,
                    'category' => VacancyCategoryEnum::FINANCE->value,
                ],
            )
            ->create();

        $this->assertCount(4, Vacancy::all());

        $dto = new FilterVacancyDto(
            search: 'web designer',
            salary_min: 1_200,
            salary_max: 2_000,
            experience: VacancyExperienceEnum::MIDDLE,
            category: VacancyCategoryEnum::IT,
        );
        /** @var Collection $vacancies */
        $vacancies = Vacancy::filter($dto)->get();

        $this->assertCount(1, $vacancies);
        $this->assertEquals('Web designer at Google', $vacancies->getIterator()->offsetGet(0)->title);
        $this->assertEquals(1_500, $vacancies->getIterator()->offsetGet(0)->salary);
    }
}
