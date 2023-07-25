<?php

namespace Tests\Feature;

use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Tests\TestHelper;

class MyVacancyControllerMethodShowTest extends TestCase
{
    use RefreshDatabase;

    public static function data(): \Generator
    {
        $uuid = Str::uuid()->toString();

        yield 'show success without vacancy' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            $uuid,
            200,
            null,
            function (TestResponse $response) use ($uuid) {
                $vacancy = Vacancy::find($uuid);
                $url = Config::get('app.url');

                $response->assertSeeInOrder([
                    e($vacancy->title),
                    '$'. number_format($vacancy->salary),
                    e($vacancy->employer->name),
                    $vacancy->location,
                    'Not found applications yet',
                ], false);

                $response->assertSeeInOrder([
                    Str::upper($vacancy->experience),
                    Str::upper($vacancy->category),
                ], false);

                $response->assertSeeInOrder([
                    'action="'.$url.'/my-vacancy/'.$uuid.'" method="post">',
                    '<input type="hidden" name="_method" value="delete">',
                    'Archive vacancy',
                ], false);

                $response->assertSeeInOrder([
                    'action="'.$url.'/my-vacancy/'.$uuid.'/force_destroy" method="post">',
                    '<input type="hidden" name="_method" value="delete">',
                    'Permanent delete',
                ], false);
            },
        ];

        yield 'show not found' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            Str::uuid()->toString(),
            404,
            null,
            []
        ];

        yield 'show with application' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid, Str::uuid()->toString(), Str::uuid()->toString()),
            $uuid,
            200,
            null,
            function (TestResponse $response) {
                $see = [];

                VacancyApplication::all()
                    ->each(function (VacancyApplication $vacancyApplication) use (&$see) {
                        $see[] = $vacancyApplication->user->name;
                        $see[] = number_format($vacancyApplication->expect_salary);
                    });

                $response->assertSee($see, false);
            }
        ];

        yield 'show with application and paginator' => [
            function () use ($uuid) {
                Config::set('app.paginator.vacancies.employer.applications', 1);

                return TestHelper::makeVacancyWithApplication(
                    $uuid,
                    Str::uuid()->toString(),
                    Str::uuid()->toString()
                );
            },
            $uuid,
            200,
            null,
            [
                '<nav role="navigation" aria-label="Pagination Navigation"',
                '/my-vacancy/' . $uuid . '?page=2"',
            ],
        ];
    }

    /** @dataProvider data */
    public function test_show(\Closure $actingAs, string $uuid, int $statusCode, ?string $redirectTo, array|\Closure $see): void
    {
        if ($user = $actingAs()) {
            $this->actingAs($user);
        }

        $response = $this->get('my-vacancy/' . $uuid)
            ->assertStatus($statusCode);

        if ($redirectTo) {
            $response->assertRedirect($redirectTo);
        } else {
            if ($see instanceof \Closure) {
                $see($response);
            } else {
                $response->assertSee($see, false);
            }
        }
    }
}
