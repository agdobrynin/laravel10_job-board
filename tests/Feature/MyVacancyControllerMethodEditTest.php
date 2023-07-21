<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TestHelper;

class MyVacancyControllerMethodEditTest extends TestCase
{

    use RefreshDatabase;

    public static function dataEdit(): \Generator
    {
        $uuid = Str::uuid()->toString();

        yield 'show edit form by anonymous' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid) ? null : false,
            $uuid,
            302,
            '/login',
        ];

        yield 'show edit form by regular user' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid) ? User::factory()->create() : false,
            $uuid,
            403,
            null,
        ];

        yield 'show edit form by unverified employer' => [
            function () use ($uuid) {
                $user = TestHelper::makeVacancyWithApplication($uuid);
                $user->email_verified_at = null;
                $user->save();

                return $user;
            },
            $uuid,
            302,
            '/email/verify',
        ];

        yield 'show edit form by employer' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            $uuid,
            200,
            null,
        ];

        yield 'show edit form not found' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            Str::uuid()->toString(),
            404,
            null,
        ];

        yield 'show edit form with applications' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid, Str::uuid()->toString()),
            $uuid,
            403,
            null,
        ];
    }

    /** @dataProvider dataEdit */
    public function test_edit_form(\Closure $actingAs, string $uuid, int $statusCode, ?string $redirectTo): void
    {
        if ($user = $actingAs()) {
            $this->actingAs($user);
        }

        $response = $this->get('/my-vacancy/' . $uuid . '/edit')
            ->assertStatus($statusCode);

        if ($redirectTo) {
            $response->assertRedirect($redirectTo);
        }
    }

    public function test_edit_form_view_same_data(): void
    {
        $uuid = Str::uuid()->toString();
        $user = TestHelper::makeVacancyWithApplication($uuid);
        $vacancy = Vacancy::find($uuid);

        $this->actingAs($user)
            ->get('/my-vacancy/' . $uuid . '/edit')
            ->assertOk()
            ->assertSeeInOrder([
                $vacancy->title,
                'Vacancy title',
                'name="title"',
            ], false)
            ->assertSeeInOrder([
                str_replace(["\n", "\r"], ["\\n", ""], $vacancy->description),
                'Vacancy description',
                'name="description"',
            ], false)
            ->assertSeeInOrder([
                $vacancy->salary,
                'Salary ($)',
                'name="salary"',
            ], false)
            ->assertSeeInOrder([
                $vacancy->location,
                'Vacancy location (city)',
                'name="location"',
            ], false);
    }
}
