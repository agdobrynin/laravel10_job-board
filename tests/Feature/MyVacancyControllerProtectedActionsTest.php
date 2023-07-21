<?php

namespace Tests\Feature;

use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MyVacancyControllerProtectedActionsTest extends TestCase
{
    use RefreshDatabase;


    public static function data(): \Generator
    {
        $vacancyUuid = Str::uuid();

        $employerFactory = User::factory()
            ->has(
                Employer::factory()
                    ->has(Vacancy::factory(['id' => $vacancyUuid]))
            );

        $employerOtherFactory = User::factory()
            ->has(Employer::factory());

        $regularUserFactory = User::factory();

        $data = [
            'title' => 'My super pure vacancy',
            'description' => fake()->paragraphs(rand(3, 6), true),
            'salary' => fake()->numberBetween(100, 1_000),
            'location' => fake()->city,
            'category' => VacancyCategoryEnum::cases()[0]->value,
            'experience' => VacancyExperienceEnum::cases()[0]->value,
        ];

        yield 'index anonymous' => [
            null,
            fn(self $that) => $that->get('/my-vacancy'),
            302,
            '/login',
        ];

        yield 'index by unverified employer' => [
            null,
            fn(self $that) => $that->actingAs($employerFactory->unverified()->create())->get('/my-vacancy'),
            302,
            '/email/verify',
        ];

        yield 'index by regular user' => [
            null,
            fn(self $that) => $that->actingAs($regularUserFactory->create())->get('/my-vacancy'),
            403,
            null,
        ];

        yield 'index by employer' => [
            null,
            fn(self $that) => $that->actingAs($employerFactory->create())->get('/my-vacancy'),
            200,
            null,
        ];

        yield 'store by anonymous' => [
            null,
            fn(self $that) => $that->post('/my-vacancy', []),
            302,
            '/login',
        ];

        yield 'store by regular user' => [
            null,
            fn(self $that) => $that->actingAs($regularUserFactory->create())->post('/my-vacancy', []),
            403,
            null,
        ];

        yield 'store by employer' => [
            null,
            fn(self $that) => $that->actingAs($employerFactory->create())->post('/my-vacancy', $data),
            302,
            '/my-vacancy',
        ];

        yield 'store by unverified employer' => [
            null,
            fn(self $that) => $that->actingAs($employerFactory->unverified()->create())->post('/my-vacancy', $data),
            302,
            '/email/verify',
        ];

        yield 'create form by anonymous' => [
            null,
            fn(self $that) => $that->get('/my-vacancy/create'),
            302,
            '/login',
        ];

        yield 'create form by regular user' => [
            null,
            fn(self $that) => $that->actingAs($regularUserFactory->create())->get('/my-vacancy/create'),
            403,
            null,
        ];

        yield 'create form by employer' => [
            null,
            fn(self $that) => $that->actingAs($employerFactory->create())->get('/my-vacancy/create'),
            200,
            null,
        ];

        yield 'create form by unverified employer' => [
            null,
            fn(self $that) => $that->actingAs($employerFactory->unverified()->create())->get('/my-vacancy/create'),
            302,
            '/email/verify',
        ];

        yield 'show vacancy by anonymous' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->get('my-vacancy/' . $vacancyUuid),
            302,
            '/login',
        ];

        yield 'show vacancy by regular user' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->actingAs($regularUserFactory->create())->get('my-vacancy/' . $vacancyUuid),
            403,
            null,
        ];

        yield 'show vacancy by employer' => [
            null,
            fn(self $that) => $that->actingAs($employerFactory->create())->get('my-vacancy/' . $vacancyUuid),
            200,
            null,
        ];

        yield 'show vacancy by unverified employer' => [
            null,
            fn(self $that) => $that->actingAs($employerFactory->unverified()->create())->get('my-vacancy/' . $vacancyUuid),
            302,
            '/email/verify',
        ];

        yield 'show vacancy by other employer' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->actingAs($employerOtherFactory->create())->get('my-vacancy/' . $vacancyUuid),
            403,
            null,
        ];

        yield 'update vacancy by anonymous' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->put('my-vacancy/' . $vacancyUuid, $data),
            302,
            '/login',
        ];

        yield 'update vacancy by regular user' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->actingAs($regularUserFactory->create())->put('my-vacancy/' . $vacancyUuid, $data),
            403,
            null,
        ];

        yield 'update vacancy by employer' => [
            fn() => $employerFactory->create(),
            function (self $that) use ($vacancyUuid, $data) {
                $user = Vacancy::find($vacancyUuid)->employer->user;

                return $that->actingAs($user)
                    ->from('my-vacancy/' . $vacancyUuid)
                    ->put('my-vacancy/' . $vacancyUuid, $data);
            },
            302,
            'my-vacancy/' . $vacancyUuid,
        ];

        yield 'update vacancy by unverified employer' => [
            fn() => $employerFactory->unverified()->create(),
            function (self $that) use ($vacancyUuid, $data) {
                $user = Vacancy::find($vacancyUuid)->employer->user;

                return $that->actingAs($user)
                    ->put('my-vacancy/' . $vacancyUuid, $data);
            },
            302,
            '/email/verify',
        ];

        yield 'update vacancy by other employer' => [
            fn() => $employerFactory->create(),
            function (self $that) use ($vacancyUuid, $data, $employerOtherFactory) {
                return $that->actingAs($employerOtherFactory->create())
                    ->put('my-vacancy/' . $vacancyUuid, $data);
            },
            403,
            null,
        ];

        yield 'delete vacancy by anonymous' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->delete('my-vacancy/' . $vacancyUuid),
            302,
            '/login',
        ];

        yield 'delete vacancy by regular user' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->actingAs($regularUserFactory->create())->delete('my-vacancy/' . $vacancyUuid),
            403,
            null,
        ];

        yield 'delete vacancy by employer' => [
            fn() => $employerFactory->create(),
            function (self $that) use ($vacancyUuid) {
                $user = Vacancy::find($vacancyUuid)->employer->user;

                return $that->actingAs($user)->delete('my-vacancy/' . $vacancyUuid);
            },
            302,
            '/my-vacancy',
        ];

        yield 'delete vacancy by unverified employer' => [
            fn() => $employerFactory->unverified()->create(),
            function (self $that) use ($vacancyUuid) {
                $user = Vacancy::find($vacancyUuid)->employer->user;

                return $that->actingAs($user)->delete('my-vacancy/' . $vacancyUuid);
            },
            302,
            '/email/verify',
        ];

        yield 'delete vacancy by other employer' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->actingAs($employerOtherFactory->create())->delete('my-vacancy/' . $vacancyUuid),
            403,
            null,
        ];

        yield 'show edit form by anonymous' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->get('my-vacancy/' . $vacancyUuid . '/edit'),
            302,
            '/login',
        ];

        yield 'show edit form by regular user' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->actingAs($regularUserFactory->create())->get('my-vacancy/' . $vacancyUuid . '/edit'),
            403,
            null,
        ];

        yield 'show edit form by employer' => [
            fn() => $employerFactory->create(),
            function (self $that) use ($vacancyUuid) {
                $user = Vacancy::find($vacancyUuid)->employer->user;

                return $that->actingAs($user)
                    ->get('my-vacancy/' . $vacancyUuid . '/edit');
            },
            200,
            null,
        ];

        yield 'show edit form by unverified employer' => [
            fn() => $employerFactory->unverified()->create(),
            function (self $that) use ($vacancyUuid) {
                $user = Vacancy::find($vacancyUuid)->employer->user;

                return $that->actingAs($user)
                    ->get('my-vacancy/' . $vacancyUuid . '/edit');
            },
            302,
            '/email/verify',
        ];

        yield 'show edit form by other employer' => [
            fn() => $employerFactory->create(),
            fn(self $that) => $that->actingAs($employerOtherFactory->create())->get('my-vacancy/' . $vacancyUuid . '/edit'),
            403,
            null,
        ];
    }

    /** @dataProvider data */
    public function test_access(
        ?\Closure $init,
        \Closure  $httpMethod,
        int       $statusCode,
        ?string   $redirectUrl,
    ): void
    {
        if ($init) {
            $init();
        };

        /** @var TestResponse $response */
        $response = $httpMethod($this);

        $response->assertStatus($statusCode);

        if ($redirectUrl) {
            $response->assertRedirect($redirectUrl);
        }
    }
}
