<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public static function data4SignIn(): \Generator
    {
        yield '# 1' => ['/auth/create', 200,];
        yield '# 2' => ['/login', 302,];
    }

    public static function data4Validate(): \Generator
    {
        yield 'is empty fields' => [
            [],
            ['email' => 'required', 'password' => 'required']
        ];

        yield 'invalid email' => [
            ['email' => 'abc', 'password' => 'pass'],
            ['email' => 'email']
        ];

        yield 'invalid email not rfc format' => [
            ['email' => 'abc@abc', 'password' => 'pass'],
            ['email' => 'email']
        ];
    }

    /**
     * @dataProvider data4SignIn
     */
    public function test_show_sign_in_form(string $url, int $statusCode): void
    {
        $response = $this->get($url)
            ->assertStatus($statusCode);

        if ($statusCode === 302) {
            $response = $this->followRedirects($response);
        }

        $response->assertSeeInOrder([
            '<title>Job bord App | Do Sign In </title>',
            '>Email</label>',
            'name="email"',
            '>Password</label>',
            'name="password"',
            'Forget password?',
        ], false);
    }

    /**
     * @dataProvider data4Validate
     */
    public function test_validate_form(array $data, array $invalid): void
    {
        $this->from('/auth')
            ->post('/auth', $data)
            ->assertRedirect('/auth')
            ->assertInvalid($invalid);
    }

    public function test_auth_success(): void
    {
        User::factory(['email' => 'oleg@mail.com', 'name' => 'Oleg Petrov'])->create();
        $this->assertNull(Auth::user());

        $response = $this->from('/auth')
            ->post('/auth', ['email' => 'oleg@mail.com', 'password' => 'password'])
            ->assertRedirect('/');


        $this->followRedirects($response)
            ->assertSee('Oleg Petrov');

        $this->assertInstanceOf(User::class, Auth::user());
        $this->assertEquals('oleg@mail.com', Auth::user()->email);

    }

    public function test_auth_invalid_credentials(): void
    {
        User::factory(['email' => 'oleg@mail.com', 'name' => 'Oleg Petrov'])->create();
        $this->assertNull(Auth::user());

        $response = $this->from('/auth')
            ->post('/auth', ['email' => 'ivan@mail.com', 'password' => 'password'])
            ->assertRedirect('/auth')
            ->assertSessionHas('error', trans('Invalid credentials'));

        $this->followRedirects($response)
            ->assertSee(trans('Invalid credentials'));

        $this->assertNull(Auth::user());

    }

    public function test_logout_unauthorized(): void
    {
        $this->delete('/auth')
            ->assertRedirect('/login');
    }

    public function test_logout_success(): void
    {
        $user = User::factory(['email' => 'ivan@ivan.dev'])->create();

        $this->actingAs($user)->delete('/auth')
            ->assertRedirect('/');
    }
}
