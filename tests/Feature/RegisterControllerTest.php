<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public static function dataValidation(): \Generator
    {
        yield 'empty fields' => [
            [],
            ['email' => 'required', 'name' => 'required', 'password' => 'required',],
            ['is_employer', 'employer_name',]
        ];

        yield 'email invalid format' => [
            ['email' => 'aaa@aaa'],
            ['email' => 'email'],
            []
        ];

        yield 'email valid format' => [
            ['email' => 'aaa@aaa.com'],
            [],
            ['email']
        ];

        yield 'email unique' => [
            ['email' => 'aaa@aaa.com'],
            ['email' => 'already'],
            [],
            fn() => User::factory(['email' => 'aaa@aaa.com'])->create(),
        ];

        yield 'user name must be min 3 characters' => [
            ['email' => 'aaa@aaa.com', 'name' => 'ab'],
            ['name' => '3 characters'],
            [],
        ];

        yield 'password min 8 characters' => [
            ['email' => 'aaa@aaa.com', 'name' => 'oleg petrov', 'password' => '1234567'],
            ['password' => '8 characters'],
            [],
        ];

        yield 'password not match' => [
            ['email' => 'aaa@aaa.com', 'name' => 'oleg petrov', 'password' => '12345678', 'password_confirmation' => 'aaa'],
            ['password' => 'not match'],
            [],
        ];

        yield 'required field employer name if check field is employer' => [
            ['is_employer' => '1'],
            ['employer_name' => 'required'],
            [],
        ];

        yield 'employer name min 5 characters' => [
            ['is_employer' => '1', 'employer_name' => 'abcd'],
            ['employer_name' => '5 characters'],
            [],
        ];

        yield 'employer name must be unique' => [
            ['is_employer' => '1', 'employer_name' => 'Kaspi Software'],
            ['employer_name' => 'already'],
            [],
            fn() => User::factory()->has(Employer::factory(['name' => 'Kaspi Software']))->create()
        ];
    }

    public function test_not_show_reg_form_for_auth_user(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/reg/create')
            ->assertRedirect('/');
    }

    public function test_show_reg_form_for_guest(): void
    {
        $url = Config::get('app.url');

        $this->get('/reg/create')
            ->assertOk()
            ->assertSeeInOrder([
                'Sign up',
                '<form action="' . $url . '/reg" method="post">',
                'name="email"',
                'name="name"',
                'name="password"',
                'name="password_confirmation"',
                'name="is_employer"',
                'name="employer_name"',
            ], false);
    }

    /** @dataProvider dataValidation */
    public function test_form_validation(array $data, array $invalid, array $valid, ?\Closure $user = null): void
    {
        if ($user) {
            $user();
        }

        $this->from('/reg/create')
            ->post('/reg', $data)
            ->assertRedirect('/reg/create')
            ->assertInvalid($invalid)
            ->assertValid($valid);
    }

    public function test_success_register_as_user(): void
    {
        // send email (notification) to user with link confirmation
        Notification::fake();

        $data = [
            'email' => 'ivan@kaspi.com',
            'name' => 'ivan ivanov',
            'password' => 'password',
            'password_confirmation' => 'password',

        ];

        $this->from('/reg/create')
            ->post('/reg', $data)
            ->assertRedirect('/vacancies')
            ->assertSessionHas('success');

        $this->assertDatabaseHas(User::class, [
            'email' => 'ivan@kaspi.com',
            'name' => 'ivan ivanov',
            'email_verified_at' => null,
        ]);

        $this->assertEquals('ivan@kaspi.com', Auth::user()->email);
        Notification::assertCount(1);

        $user = User::where('email', 'ivan@kaspi.com')->first();

        Notification::assertSentTo(
            [$user], VerifyEmail::class
        );
    }

    public function test_success_register_as_employer(): void
    {
        // send email (notification) to user with link confirmation
        Notification::fake();

        $data = [
            'email' => 'ivan@kaspi.com',
            'name' => 'ivan ivanov',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_employer' => '1',
            'employer_name' => 'Kaspi-Soft LLC'
        ];

        $this->from('/reg/create')
            ->post('/reg', $data)
            ->assertRedirect('/vacancies')
            ->assertSessionHas('success');

        $user = User::where('email', 'ivan@kaspi.com')->first();

        $this->assertDatabaseHas(User::class, [
            'email' => 'ivan@kaspi.com',
            'name' => 'ivan ivanov',
            'email_verified_at' => null,
        ]);

        $this->assertDatabaseHas(Employer::class, [
            'name' => 'Kaspi-Soft LLC',
            'user_id' => $user->id,
        ]);

        $this->assertEquals('ivan@kaspi.com', Auth::user()->email);
        Notification::assertCount(1);

        Notification::assertSentTo(
            [$user], VerifyEmail::class
        );
    }
}
