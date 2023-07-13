<?php

namespace Tests\Feature;

use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use App\Http\Requests\VacanciesIndexRequest;
use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class VacancyControllerMethodIndexTest extends TestCase
{

    use RefreshDatabase;

    public static function filterData(): \Generator
    {
        yield 'all filters empty' => [
            200,
            [],
            []
        ];

        yield 'all filters with keys and empty values' => [
            200,
            ['search' => '', 'salary_min' => '', 'salary_max' => '', 'experience' => '', 'category' => ''],
            []
        ];

        yield 'filter search is too short 1 char' => [
            302,
            ['search' => 'a'],
            ['search']
        ];

        yield 'filter search is too short 2 chars' => [
            302,
            ['search' => 'ac'],
            ['search']
        ];

        yield 'filter search good' => [
            200,
            ['search' => 'google'],
            []
        ];

        yield 'min salary must be integer' => [
            302,
            ['salary_min' => 'abc'],
            ['salary_min' => 'integer']
        ];

        yield 'min salary must more zero' => [
            302,
            ['salary_min' => 0],
            ['salary_min' => 'min']
        ];

        yield 'max salary must be integer' => [
            302,
            ['salary_max' => 'abc'],
            ['salary_max' => 'integer']
        ];

        yield 'max salary must more then min salary' => [
            302,
            ['salary_min' => 10, 'salary_max' => 5],
            ['salary_max']
        ];

        yield 'experience wrong data' => [
            302,
            ['experience' => 'good'],
            ['experience' => 'in']
        ];

        yield 'category wrong data' => [
            302,
            ['category' => 'big'],
            ['category' => 'in']
        ];
    }

    public function test_rules(): void
    {
        $this->assertEquals(
            [
                'search' => 'nullable|string|min:3',
                'salary_min' => 'nullable|integer|min:1',
                'salary_max' => [
                    'nullable',
                    'integer',
                    Rule::when(fn($attr) => (bool)$attr['salary_min'], 'gte:salary_min'),
                ],
                'experience' => ['nullable', Rule::in(VacancyExperienceEnum::values())],
                'category' => ['nullable', Rule::in(VacancyCategoryEnum::values())],
            ]
            ,
            (new VacanciesIndexRequest())->rules()
        );
    }

    /**
     * Test only request form validator for this route.
     *
     * @dataProvider filterData
     *
     */
    public function test_filter_validate(int $statusCode, array $filter, array $invalid): void
    {
        /**
         * Filter data test in \Tests\Feature\Models\VacancyTest::class
         */
        $response = $this->from('/vacancies')
            ->get('/vacancies?' . http_build_query($filter))
            ->assertStatus($statusCode);

        if ($invalid) {
            $response->assertInvalid($invalid);
        }
    }

    public function test_paginator(): void
    {
        $url = Config::get('app.url');

        User::factory(2)
            ->has(
                Employer::factory()
                    ->has(Vacancy::factory(5))
            )->create();

        Config::set('app.paginator.vacancies.list', 5);

        $vacanciesOnFirstPage = Vacancy::latest()
            ->take(Config::get('app.paginator.vacancies.list'))
            ->get()
            ->pluck('title')
            ->toArray();


        $this->get('/vacancies')
            ->assertOk()
            ->assertSeeInOrder([
                '<nav role="navigation" aria-label="Pagination Navigation"',
                '<a href="' . $url . '/vacancies?page=2"',
            ], false)
            ->assertDontSee([
                '<a href="' . $url . '/vacancies?page=1"',
                '<a href="' . $url . '/vacancies?page=3"',
            ], false)
            ->assertSeeTextInOrder(
                $vacanciesOnFirstPage
            );

        $vacanciesOnSecondPage = Vacancy::latest()
            ->skip(5)
            ->take(Config::get('app.paginator.vacancies.list'))
            ->get()
            ->pluck('title')
            ->toArray();

        $this->get('/vacancies?page=2')
            ->assertOk()
            ->assertSeeInOrder([
                '<nav role="navigation" aria-label="Pagination Navigation"',
                '<a href="' . $url . '/vacancies?page=1"'
            ], false)
            ->assertDontSee([
                '<a href="' . $url . '/vacancies?page=2"',
                '<a href="' . $url . '/vacancies?page=3"',
            ], false)
            ->assertSeeTextInOrder($vacanciesOnSecondPage);
    }
}
