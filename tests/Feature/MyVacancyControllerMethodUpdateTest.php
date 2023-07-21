<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TestHelper;

class MyVacancyControllerMethodUpdateTest extends TestCase
{
    use RefreshDatabase;

    public static function validationData(): \Generator
    {
        $uuid = Str::uuid()->toString();

        yield 'empty form' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            $uuid,
            [],
            302,
            [
                'title' => 'required',
                'description' => 'required',
                'salary' => 'required',
                'location' => 'required',
                'experience' => 'required',
                'category' => 'required',
            ],
            [],
        ];

        yield 'empty values' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            $uuid,
            [
                'title' => '',
                'description' => '',
                'salary' => '',
                'location' => '',
                'experience' => '',
                'category' => '',
            ],
            302,
            [
                'title' => 'required',
                'description' => 'required',
                'salary' => 'required',
                'location' => 'required',
                'experience' => 'required',
                'category' => 'required',
            ],
            [],
        ];

        yield 'short values' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            $uuid,
            [
                'title' => 'a',
                'description' => 'b',
                'salary' => '0',
                'location' => 'a',
                'experience' => 'junior',
                'category' => 'it',
            ],
            302,
            [
                'title' => '10',
                'description' => '50',
                'salary' => '1',
                'location' => '5',
            ],
            [
                'experience',
                'category',
            ],
        ];

        yield 'wrong values' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            $uuid,
            [
                'title' => 'Some title of vacancy',
                'description' => fake()->paragraph(),
                'salary' => 'many',
                'location' => 'Port Hump, FL',
                'experience' => 'big boss',
                'category' => 'space',
            ],
            302,
            [
                'salary' => 'integer',
                'experience' => 'in',
                'category' => 'in'
            ],
            [
                'title',
                'description',
            ],
        ];

        yield 'success' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid),
            $uuid,
            [
                'title' => 'Some title of vacancy',
                'description' => fake()->paragraph(),
                'salary' => 1_350,
                'location' => 'Port Hump, FL',
                'experience' => 'junior',
                'category' => 'it',
            ],
            302,
            [],
            [
                'title',
                'description',
                'salary',
                'location',
                'experience',
                'category',
            ],
        ];

        yield 'can not update if has application' => [
            fn() => TestHelper::makeVacancyWithApplication($uuid, Str::uuid()),
            $uuid,
            [],
            403,
            [],
            [],
        ];
    }

    /**
     * @dataProvider validationData
     */
    public function test_validation(\Closure $fnUser, string $uuid, array $data, int $statusCode, array $invalid, array $valid): void
    {
        $user = $fnUser();

        $response = $this->actingAs($user)
            ->put('/my-vacancy/' . $uuid, $data)
            ->assertStatus($statusCode);

        if ($invalid) {
            $response->assertInvalid($invalid);
        } else {
            $this->assertDatabaseHas(Vacancy::class, $data);
        }

        if ($valid) {
            $response->assertValid($valid);
        }
    }

    public function test_by_user(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);

        $this->actingAs(User::factory()->create())
            ->put('/my-vacancy/' . $uuid, [])
            ->assertForbidden();
    }

    public function test_by_anonymous(): void
    {
        $uuid = Str::uuid();

        $this->put('/my-vacancy/' . $uuid, [])
            ->assertRedirect('/login');
    }

    public function test_by_other_employer(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);

        $this->actingAs(User::factory()->has(Employer::factory())->create())
            ->put('/my-vacancy/' . $uuid, [])
            ->assertForbidden();
    }

    public function test_not_found(): void
    {
        $uuid = Str::uuid();

        $this->actingAs(User::factory()->has(Employer::factory())->create())
            ->put('/my-vacancy/' . $uuid, [])
            ->assertNotFound();
    }
}
