<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Policies\VacancyApplicationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelper;

class VacancyApplicationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_ability_view_success(): void
    {
        $user = User::factory()->create();
        $application = TestHelper::makeApplication($user);

        $this->assertTrue((new VacancyApplicationPolicy())->view($user, $application));
    }

    public function test_ability_view_deny(): void
    {
        $application = TestHelper::makeApplication(User::factory()->create());

        $this->assertFalse((new VacancyApplicationPolicy())->view(User::factory()->create(), $application));
    }

    public function test_ability_delete_success(): void
    {
        $user = User::factory()->create();
        $application = TestHelper::makeApplication($user);

        $this->assertTrue((new VacancyApplicationPolicy())->delete($user, $application));
    }

    public function test_ability_delete_deny(): void
    {
        $application = TestHelper::makeApplication(User::factory()->create());

        $this->assertFalse((new VacancyApplicationPolicy())->delete(User::factory()->create(), $application));
    }

    public function test_force_delete(): void
    {
        $user = User::factory()->create();
        $application = TestHelper::makeApplication($user);

        $res = (new VacancyApplicationPolicy())->forceDelete($user, $application);

        $this->assertTrue($res);
    }

    public function test_force_delete_by_other_user(): void
    {
        $user = User::factory()->create();
        $application = TestHelper::makeApplication(User::factory()->create());

        $res = (new VacancyApplicationPolicy())->forceDelete($user, $application);

        $this->assertFalse($res);
    }
}
