<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TestHelper;

class MyVacancyControllerMethodDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy_not_found(): void
    {
        $uuid = Str::uuid();

        $this->actingAs(User::factory()->has(Employer::factory())->create())
            ->delete('/my-vacancy/' . $uuid)
            ->assertNotFound();
    }

    public function test_destroy_with_application(): void
    {
        $uuid = Str::uuid();
        $uuidApplication = Str::uuid();

        $user = TestHelper::makeVacancyWithApplication($uuid, $uuidApplication);

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);
        $this->assertDatabaseHas(VacancyApplication::class, ['id' => $uuidApplication]);

        $this->actingAs($user)
            ->delete('/my-vacancy/' . $uuid)
            ->assertRedirect('/my-vacancy')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing(Vacancy::class, ['id' => $uuid]);
        $this->assertDatabaseMissing(VacancyApplication::class, ['id' => $uuidApplication]);
    }

    public function test_destroy_by_not_owner_employer(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);

        $this->actingAs(User::factory()->has(Employer::factory())->create())
            ->delete('/my-vacancy/' . $uuid)
            ->assertForbidden();

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);
    }

    public function test_destroy(): void
    {
        $uuid = Str::uuid();

        $user = TestHelper::makeVacancyWithApplication($uuid);

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);

        $this->actingAs($user)
            ->delete('/my-vacancy/' . $uuid)
            ->assertRedirect('/my-vacancy')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing(Vacancy::class, ['id' => $uuid]);
    }

    public function test_destroy_by_user(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);

        $this->actingAs(User::factory()->create())
            ->delete('/my-vacancy/' . $uuid)
            ->assertForbidden();

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);
    }
}
