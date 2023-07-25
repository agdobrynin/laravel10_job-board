<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TestHelper;

class MyVacancyApplicationControllerTest extends TestCase
{

    use RefreshDatabase;

    public static function dataDownload(): \Generator
    {
        $applicationUuid = Str::uuid()->toString();

        yield 'by anonymous' => [
            fn() => null,
            $applicationUuid,
            302,
            '/login'
        ];

        yield 'by not owner' => [
            function () use ($applicationUuid) {
                TestHelper::makeVacancyWithApplication(Str::uuid(), $applicationUuid);

                return User::factory()->create();
            },
            $applicationUuid,
            403,
        ];

        yield 'success' => [
            function () use ($applicationUuid) {
                TestHelper::makeVacancyWithApplication(Str::uuid(), $applicationUuid);
                $application = VacancyApplication::find($applicationUuid);
                TestHelper::attachCvToApplication($application);

                return $application->user;
            },
            $applicationUuid,
            200,
        ];
    }

    public function test_my_vacancy_by_anonymous(): void
    {
        $this->get('/my-vacancy-applications')
            ->assertRedirect('/login');
    }

    public function test_my_vacancy_by_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/my-vacancy-applications')
            ->assertOk()
            ->assertSeeInOrder([
                'My vacancies applications',
                e($user->name),
                'Logout',
                'Applications to vacancies not found.'
            ], false);
    }

    public function test_my_vacancy_by_unverified_user(): void
    {
        $user = User::factory(['name' => 'Super Man'])->unverified()->create();

        $response = $this->actingAs($user)
            ->get('/my-vacancy-applications')
            ->assertRedirect('/email/verify');

        $this->followRedirects($response)
            ->assertSeeInOrder([
                $user->name,
                'Logout',
                'Your email not confirmed',
                '/email/verification-notification" method="post">'
            ], false);
    }

    public function test_my_vacancy_by_user_with_pagination(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        VacancyApplication::factory(4)
            ->for($user)
            ->for(
                Vacancy::factory()
                    ->for(
                        Employer::factory()
                            ->for(
                                User::factory()->create()
                            )->create()
                    )->create()
            )
            ->create();

        $user2 = User::factory()->create();

        VacancyApplication::factory(4)
            ->for($user2)
            ->for(
                Vacancy::factory()
                    ->for(
                        Employer::factory()
                            ->for(
                                User::factory()->create()
                            )->create()
                    )->create()
            )
            ->create();

        Config::set('app.paginator.my_vacancy_applications.list', 2);
        $url = Config::get('app.url');

        $this->actingAs($user)
            ->get('/my-vacancy-applications')
            ->assertOk()
            ->assertSeeInOrder([
                '<nav role="navigation" aria-label="Pagination Navigation"',
                '<a href="' . $url . '/my-vacancy-applications?page=2"',
            ], false)
            ->assertDontSee([
                '<a href="' . $url . '/my-vacancy-applications?page=1"',
                '<a href="' . $url . '/my-vacancy-applications?page=3"',
            ], false);

        // go to page 2
        $this->actingAs($user)
            ->get('/my-vacancy-applications?page=2')
            ->assertOk()
            ->assertSeeInOrder([
                '<nav role="navigation" aria-label="Pagination Navigation"',
                '<a href="' . $url . '/my-vacancy-applications?page=1"',
            ], false)
            ->assertDontSee([
                '<a href="' . $url . '/my-vacancy-applications?page=2"',
                '<a href="' . $url . '/my-vacancy-applications?page=3"',
            ], false);
    }

    public function test_destroy_fail(): void
    {
        $vacancyUuid = Str::uuid()->toString();
        $applicationUuid = Str::uuid()->toString();

        TestHelper::makeVacancyWithApplication($vacancyUuid, $applicationUuid);

        $this->actingAs(User::factory()->create())
            ->delete('my-vacancy-applications/' . $applicationUuid)
            ->assertForbidden();
    }

    public function test_destroy_with_cv(): void
    {
        $vacancyUuid = Str::uuid()->toString();
        $applicationUuid = Str::uuid()->toString();

        Storage::fake('cv');

        TestHelper::makeVacancyWithApplication($vacancyUuid, $applicationUuid);
        $application = VacancyApplication::find($applicationUuid);
        $cvPath = TestHelper::attachCvToApplication($application);

        $this->assertDatabaseHas(
            VacancyApplication::class, [
            'id' => $applicationUuid,
            'cv_path' => $cvPath,
        ]);

        Storage::disk('cv')->assertExists($cvPath);

        $this->actingAs($application->user)
            ->from('/my-vacancy-applications')
            ->delete('my-vacancy-applications/' . $applicationUuid)
            ->assertRedirect('/my-vacancy-applications')
            ->assertSessionHas('success');

        Storage::disk('cv')->assertMissing($cvPath);

        $this->assertDatabaseMissing(VacancyApplication::class, ['id' => $applicationUuid]);
    }

    /** @dataProvider dataDownload */
    public function test_download(
        \Closure $initUser,
        string   $uuid,
        int      $statusCode,
        ?string  $redirectTo = null,
    ): void
    {
        if ($user = $initUser()) {
            $this->actingAs($user);
        }

        $response = $this->get("/my-vacancy-applications/{$uuid}/download")
            ->assertStatus($statusCode);

        if ($statusCode === 200) {
            $response->assertDownload(VacancyApplication::find($uuid)->cv_path);
        }

        if ($redirectTo) {
            $response->assertRedirect($redirectTo);
        }
    }
}
