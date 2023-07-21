<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MyVacancyControllerMethodIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_vacancy(): void
    {
        $user = User::factory()
            ->has(Employer::factory())
            ->create();

        $this->actingAs($user)
            ->get('/my-vacancy')
            ->assertOk()
            ->assertSee('Vacancies not found');
    }

    public function test_pagination(): void
    {
        $user = User::factory()
            ->has(
                Employer::factory()
                    ->has(Vacancy::factory(2))
            )
            ->create();

        Config::set('app.paginator.vacancies.list', 1);

        $this->actingAs($user)
            ->get('/my-vacancy')
            ->assertOk()
            ->assertSee([
                '<nav role="navigation" aria-label="Pagination Navigation"',
                '/my-vacancy?page=2"',
            ], false)
            ->assertSee([
                'View applications',
                'Edit vacancy',
                'Delete vacancy',
            ], false)
            ->assertDontSee([
                '/my-vacancy?page=1"',
                '/my-vacancy?page=3"',
            ], false);
    }
}
