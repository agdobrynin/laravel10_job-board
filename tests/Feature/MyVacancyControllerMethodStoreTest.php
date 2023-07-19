<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyVacancyControllerMethodStoreTest extends TestCase
{

    use RefreshDatabase;

    public static function data(): \Generator
    {
        $actingAs = fn() => User::factory()->has(Employer::factory())->create();

        yield 'form without data' => [
            $actingAs,
            302,
            [],
            [
                'title' => 'required',
                'description' => 'required',
                'salary' => 'required',
                'location' => 'required',
                'experience' => 'required',
                'category' => 'required',
            ],
            null,
            null,
        ];

        yield 'form empty form' => [
            $actingAs,
            302,
            [
                'title' => '',
                'description' => '',
                'salary' => '',
                'location' => '',
                'experience' => '',
                'category' => '',
            ],
            [
                'title' => 'required',
                'description' => 'required',
                'salary' => 'required',
                'location' => 'required',
                'experience' => 'required',
                'category' => 'required',
            ],
            null,
            null,
        ];

        yield 'form form with wrong data' => [
            $actingAs,
            302,
            [
                'title' => 'short',
                'description' => 'short',
                'salary' => '0',
                'location' => '?',
                'experience' => 'a',
                'category' => 'b',
            ],
            [
                'title' => '10',
                'description' => '50',
                'salary' => '1',
                'location' => '5',
                'experience' => 'invalid',
                'category' => 'invalid',
            ],
            null,
            null,
        ];

        yield 'form success' => [
            $actingAs,
            302,
            [
                'title' => 'First vacancy title',
                'description' => 'Compellingly productize customer directed

                                  technologies without timely products.

                                  Compellingly empower compelling growth

                                  strategies with value-added',
                'salary' => '1000',
                'location' => 'San Hose',
                'experience' => 'junior',
                'category' => 'it',
            ],
            null,
            ['title', 'description', 'salary', 'location', 'experience', 'category'],
            'success',
        ];
    }

    /** @dataProvider data */
    public function test_validation(
        \Closure $actingAs,
        int      $statusCode,
        array    $data,
        ?array   $invalid,
        ?array   $valid,
        ?string  $successFlushKey = null,
    ): void
    {
        $user = $actingAs();

        $response = $this->actingAs($user)
            ->from('/my-vacancy/create')
            ->post('/my-vacancy', $data)
            ->assertStatus($statusCode);

        if ($invalid) {
            $response->assertInvalid($invalid);
        }

        if ($valid) {
            $response->assertValid($valid);
        }

        if ($successFlushKey) {
            $response->assertSessionHas($successFlushKey);
            $this->assertDatabaseHas(Vacancy::class, $data);
        }
    }
}
