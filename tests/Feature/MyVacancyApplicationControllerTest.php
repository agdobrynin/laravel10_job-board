<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MyVacancyApplicationControllerTest extends TestCase
{

    use RefreshDatabase;

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
                $user->name,
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
}
