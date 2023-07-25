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

class MyVacancyControllerMethodForceDestroyTest extends TestCase
{
    use RefreshDatabase;


    public function test_not_found(): void
    {
        $uuid = Str::uuid();

        $this->actingAs(User::factory()->has(Employer::factory())->create())
            ->delete("/my-vacancy/{$uuid}/force_destroy")
            ->assertNotFound();
    }

    public function test_by_anonymous(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);

        $this->delete("/my-vacancy/{$uuid}/force_destroy")
            ->assertRedirect('/login');
    }

    public function test_by_regular_user(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);

        $this->actingAs(User::factory()->create())
            ->delete("/my-vacancy/{$uuid}/force_destroy")
            ->assertForbidden();

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);
    }

    public function test_by_other_employer(): void
    {
        $uuid = Str::uuid();
        $applicationUuid = Str::uuid();
        Storage::fake('cv');

        TestHelper::makeVacancyWithApplication($uuid, $applicationUuid);
        $cvPath = TestHelper::attachCvToApplication(VacancyApplication::find($applicationUuid));

        $this->actingAs(User::factory()->has(Employer::factory())->create())
            ->delete("/my-vacancy/{$uuid}/force_destroy")
            ->assertForbidden();

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);
        $this->assertDatabaseHas(VacancyApplication::class, ['id' => $applicationUuid]);
        Storage::disk('cv')->assertExists($cvPath);
    }

    public function test_by_employer(): void
    {
        $uuid = Str::uuid();
        $applicationUuid = Str::uuid();
        Storage::fake('cv');

        $user = TestHelper::makeVacancyWithApplication($uuid, $applicationUuid);
        $cvPath = TestHelper::attachCvToApplication(VacancyApplication::find($applicationUuid));
        Storage::disk('cv')->assertExists($cvPath);

        $this->actingAs($user)
            ->delete("/my-vacancy/{$uuid}/force_destroy")
            ->assertRedirect('/my-vacancy');

        $this->assertDatabaseMissing(Vacancy::class, ['id' => $uuid]);
        $this->assertDatabaseMissing(VacancyApplication::class, ['id' => $applicationUuid]);
        Storage::disk('cv')->assertMissing($cvPath);
    }

    public function test_by_employer_unverified(): void
    {
        $uuid = Str::uuid();
        $applicationUUid = Str::uuid();

        $user = TestHelper::makeVacancyWithApplication($uuid, $applicationUUid);
        $user->email_verified_at = null;
        $user->save();

        $this->actingAs($user)
            ->delete("/my-vacancy/{$uuid}/force_destroy")
            ->assertRedirect('/email/verify');

        $this->assertDatabaseHas(Vacancy::class, ['id' => $uuid]);
        $this->assertDatabaseHas(VacancyApplication::class, ['id' => $applicationUUid]);
    }
}
