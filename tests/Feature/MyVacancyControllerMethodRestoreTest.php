<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TestHelper;

class MyVacancyControllerMethodRestoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_found(): void
    {
        $uuid = Str::uuid();

        $this->actingAs(User::factory()->has(Employer::factory())->create())
            ->get("/my-vacancy/{$uuid}/restore")
            ->assertNotFound();
    }

    public function test_by_anonymous(): void
    {
        $uuid = Str::uuid();

        $this->get("/my-vacancy/{$uuid}/restore")
            ->assertRedirect('/login');
    }

    public function test_by_regular_user(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);
        Vacancy::find($uuid)->delete();

        $this->actingAs(User::factory()->create())
            ->get("/my-vacancy/{$uuid}/restore")
            ->assertForbidden();

        $this->assertSoftDeleted(Vacancy::class, ['id' => $uuid]);
    }

    public function test_by_other_employer(): void
    {
        $uuid = Str::uuid();
        $applicationUuid = Str::uuid();

        TestHelper::makeVacancyWithApplication($uuid, $applicationUuid);
        Vacancy::find($uuid)->delete();

        $this->actingAs(User::factory()->has(Employer::factory())->create())
            ->get("/my-vacancy/{$uuid}/restore")
            ->assertForbidden();

        $this->assertSoftDeleted(Vacancy::class, ['id' => $uuid]);
        $this->assertSoftDeleted(VacancyApplication::class, ['id' => $applicationUuid]);
    }

    public function test_by_employer(): void
    {
        Storage::fake('cv');
        $uuid = Str::uuid();
        $applicationUuid = Str::uuid();

        $user = TestHelper::makeVacancyWithApplication($uuid, $applicationUuid);
        $cvPath = TestHelper::attachCvToApplication(VacancyApplication::find($applicationUuid));
        Vacancy::find($uuid)->delete();

        Storage::disk('cv')->assertExists($cvPath);
        $this->assertSoftDeleted(Vacancy::class, ['id' => $uuid]);
        $this->assertSoftDeleted(VacancyApplication::class, ['id' => $applicationUuid]);

        $this->actingAs($user)
            ->get("/my-vacancy/{$uuid}/restore")
            ->assertRedirect('/my-vacancy')
            ->assertSessionHas('success');

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);
        $this->assertDatabaseHas(VacancyApplication::class, ['id' => $applicationUuid]);
        Storage::disk('cv')->assertExists($cvPath);
    }
}
