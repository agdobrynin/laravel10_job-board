<?php

namespace Tests\Feature\Policies;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Policies\VacancyPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacancyPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_any_anonymous(): void
    {
        $this->assertTrue((new VacancyPolicy())->viewAny(null));
    }

    public function test_view_any_user(): void
    {
        $this->assertTrue((new VacancyPolicy())->viewAny(User::factory()->create()));
    }

    public function test_view_anonymous(): void
    {
        $vacancy = $this->makeVacancy(User::factory()->create());

        $this->assertTrue((new VacancyPolicy())->view(null, $vacancy));
    }

    private function makeVacancy(User $user): Vacancy
    {
        return Vacancy::factory()
            ->for(
                Employer::factory()
                    ->for(
                        $user
                    )
            )->create();
    }

    public function test_view_user(): void
    {
        $user = User::factory()->create();

        $vacancy = $this->makeVacancy(User::factory()->create());

        $this->assertTrue((new VacancyPolicy())->view($user, $vacancy));
    }

    public function test_create_without_employer(): void
    {
        $user = User::factory()->create();

        $this->assertFalse((new VacancyPolicy())->create($user));
    }

    public function test_create_as_employer(): void
    {
        $user = User::factory()->has(Employer::factory())->create();

        $this->assertTrue((new VacancyPolicy())->create($user));
    }

    public function test_update(): void
    {
        $user = User::factory()->create();

        $vacancy = $this->makeVacancy(User::factory()->create());

        $this->assertFalse((new VacancyPolicy())->update($user, $vacancy));

        $this->expectException(\TypeError::class);
        $this->assertTrue((new VacancyPolicy())->update(null, $vacancy));
    }

    public function test_update_as_employer(): void
    {
        $user = User::factory()->create();

        $vacancy = $this->makeVacancy($user);

        $this->assertTrue((new VacancyPolicy())->update($user, $vacancy));
    }

    public function test_delete(): void
    {
        $user = User::factory()->create();

        $vacancy = $this->makeVacancy(User::factory()->create());

        $this->assertFalse((new VacancyPolicy())->delete($user, $vacancy));

        $this->expectException(\TypeError::class);
        $this->assertTrue((new VacancyPolicy())->delete(null, $vacancy));
    }

    public function test_delete_as_employer(): void
    {
        $user = User::factory()->create();

        $vacancy = $this->makeVacancy($user);

        $this->assertTrue((new VacancyPolicy())->delete($user, $vacancy));
    }

    public function test_restore(): void
    {
        $user = User::factory()->create();

        $vacancy = $this->makeVacancy(User::factory()->create());

        $this->assertFalse((new VacancyPolicy())->restore($user, $vacancy));

        $this->expectException(\TypeError::class);
        $this->assertTrue((new VacancyPolicy())->restore(null, $vacancy));
    }

    public function test_forceDelete(): void
    {
        $user = User::factory()->create();

        $vacancy = $this->makeVacancy(User::factory()->create());

        $this->assertFalse((new VacancyPolicy())->forceDelete($user, $vacancy));

        $this->expectException(\TypeError::class);
        $this->assertTrue((new VacancyPolicy())->forceDelete(null, $vacancy));
    }

    public function test_forceDelete_as_employer(): void
    {
        $user = User::factory()->create();

        $vacancy = $this->makeVacancy($user);

        $this->assertTrue((new VacancyPolicy())->forceDelete($user, $vacancy));
    }

    public function test_apply_anonymous(): void
    {
        $vacancy = $this->makeVacancy(User::factory()->create());

        $res = (new VacancyPolicy())->apply(null, $vacancy);

        $this->assertInstanceOf(Response::class, $res);
        $this->assertFalse($res->denied());
    }

    public function test_apply_user_not_applied(): void
    {
        $user = User::factory()->create();
        $vacancy = $this->makeVacancy(User::factory()->create());

        $res = (new VacancyPolicy())->apply($user, $vacancy);

        $this->assertInstanceOf(Response::class, $res);
        $this->assertFalse($res->denied());
    }

    public function test_apply_user_already_applied(): void
    {
        $user = User::factory()->create();
        $vacancy = $this->makeVacancy(User::factory()->create());

        VacancyApplication::factory()
            ->for($user)
            ->for($vacancy)
            ->create();

        $res = (new VacancyPolicy())->apply($user, $vacancy);

        $this->assertInstanceOf(Response::class, $res);
        $this->assertTrue($res->denied());
        $this->assertEquals('Already applied to this vacancy', $res->message());
    }
}
