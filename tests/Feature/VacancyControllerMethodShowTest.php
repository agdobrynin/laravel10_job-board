<?php

namespace Tests\Feature;

use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class VacancyControllerMethodShowTest extends TestCase
{

    use RefreshDatabase;
    public function test_not_found(): void
    {
        $this->get('/vacancies/' . Str::uuid())
            ->assertNotFound();
    }

    public function test__without_other_vacancies(): void
    {
        $uuid = Str::uuid();

        User::factory()
            ->has(
                Employer::factory(['name' => 'Cummerata Group'])
                    ->has(Vacancy::factory(
                        [
                            'id' => $uuid,
                            'salary' => 1_050,
                            'title' => 'Human Director',
                            'category' => VacancyCategoryEnum::MARKETING->value,
                            'experience' => VacancyExperienceEnum::SENIOR->value,
                        ])
                    )
            )->create();

        $this->get('/vacancies/' . $uuid)
            ->assertOk()
            ->assertSeeTextInOrder([
                'Human Director',
                'Salary: $1,050',
                'Employer: Cummerata Group',
                Str::upper(VacancyExperienceEnum::SENIOR->value),
                Str::upper(VacancyCategoryEnum::MARKETING->value),
            ])
            ->assertDontSeeText([
                'Other vacancies from'
            ]);
    }

    public function test__has_other_vacancies_with_paginator(): void
    {
        $uuid = Str::uuid();
        $employerTitle = 'JetBrains s.r.o.';

        User::factory()
            ->has(
                Employer::factory(['name' => $employerTitle])
                    ->has(
                        Vacancy::factory(10)
                            ->sequence(
                                fn(Sequence $sequence) => $sequence->index ? ['id' => Str::uuid()] : ['id' => $uuid]
                            )
                        ->state(['created_at' => now()->addMinutes(rand(1,2))])
                    )
            )->create();

        Config::set('app.paginator.vacancies.employer.vacancies', 5);


        $response = $this->get('/vacancies/' . $uuid)
            ->assertOk()
            ->assertSee([
                'Other vacancies from &laquo;' . $employerTitle . '&raquo;',
                '<nav role="navigation" aria-label="Pagination Navigation"',
                '/vacancies/' . $uuid . '?page=2"'
            ], false)
            ->assertDontSee([
                '/vacancies/' . $uuid . '?page=1"',
                '/vacancies/' . $uuid . '?page=3"'
            ], false);

        $vacanciesTitles = Vacancy::latest()
            ->limit(5)
            ->get()
            ->pluck('title');

        $response->assertSeeTextInOrder($vacanciesTitles->toArray(), false);
    }
}
