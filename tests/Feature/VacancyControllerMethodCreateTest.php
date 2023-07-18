<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacancyControllerMethodCreateTest extends TestCase
{
    use RefreshDatabase;

    public static function data(): \Generator
    {
        yield 'anonymous' => [
            fn() => null,
            403,
            [],
            null,
        ];

        yield 'user but not employer' => [
            fn() => User::factory()->create(),
            403,
            [],
            null,
        ];

        yield 'user employer but not verified email' => [
            fn() => User::factory()->unverified()->has(Employer::factory())->create(),
            302,
            [],
            '/email/verify',
        ];

        yield 'user employer' => [
            fn() => User::factory()->has(Employer::factory())->create(),
            200,
            [
                'Create vacancy',
                'Vacancy title',
                'name="title"',
                'Vacancy description',
                'name="description"',
                'Salary ($)',
                'name="salary"',
                'Vacancy location (city)',
                'name="location"',
                'name="experience"',
                'name="category"'
            ],
            null,
        ];
    }

    /** @dataProvider data */
    public function test_available_form_for_employer_only(
        \Closure $actingAs,
        int      $statusCode,
        array    $see,
        ?string  $redirectTo,
    ): void
    {
        if ($actingUser = $actingAs()) {
            $this->actingAs($actingUser);
        }

        $response = $this->get('/vacancies/create')
            ->assertStatus($statusCode)
            ->assertSee($see, false);

        if ($response->isRedirection()) {
            $response->assertRedirect($redirectTo);
        }
    }
}
