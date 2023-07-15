<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class VacancyApplicationControllerTest extends TestCase
{
    use RefreshDatabase;

    public static function dataForValidation(): \Generator
    {
        yield 'salary expect required' => [
            [],
            ['expect_salary' => 'required'],
            [],
        ];

        yield 'salary expect must be integer' => [
            ['expect_salary' => 'abc'],
            ['expect_salary' => 'integer'],
            [],
        ];


        yield 'salary expect must more zero #1' => [
            ['expect_salary' => -1],
            ['expect_salary'],
            [],
        ];

        yield 'salary expect must more zero #2' => [
            ['expect_salary' => 0],
            ['expect_salary'],
            [],
        ];

        yield 'salary success' => [
            ['expect_salary' => 10_000],
            [],
            ['expect_salary']
        ];

        yield 'salary success if input as string with spaces' => [
            ['expect_salary' => ' 10000 '],
            [],
            ['expect_salary']
        ];

        yield 'salary must be less or equal then 1 million' => [
            ['expect_salary' => 1_000_000],
            [],
            ['expect_salary'],
        ];

        yield 'salary 10 million' => [
            ['expect_salary' => 1_000_001],
            ['expect_salary'],
            []
        ];
    }

    public function test_create_form_unauthorized_user(): void
    {
        $this->get('/vacancies/' . $this->makeVacancyUuid() . '/application/create')
            ->assertRedirect('/login');
    }

    protected function makeVacancyUuid(): string
    {
        $uuid = Str::uuid();

        User::factory()
            ->has(
                Employer::factory()
                    ->has(
                        Vacancy::factory(['id' => $uuid])
                    )
            )->create();

        return $uuid;
    }

    public function test_create_form_for_user(): void
    {
        $uuid = $this->makeVacancyUuid();
        $vacancy = Vacancy::find($uuid);
        $url = Config::get('app.url');

        $this->actingAs(User::factory()->create())
            ->get('/vacancies/' . $uuid . '/application/create')
            ->assertOk()
            ->assertSeeInOrder([
                '>' . $vacancy->title . '</h2>',
                '>Salary: $' . number_format($vacancy->salary) . '</div>',
                'Your Job Application',
                '<form action="' . $url . '/vacancies/' . $uuid . '/application" method="post">',
                '>Expected Salary</label>',
                'name="expect_salary"'
            ], false);
    }

    public function test_create_form_for_exist_application(): void
    {
        $uuid = $this->makeVacancyUuid();
        $user = User::factory()->create();

        VacancyApplication::factory(['user_id' => $user->id, 'vacancy_id' => $uuid])
            ->create();


        $this->actingAs($user)
            ->get('/vacancies/' . $uuid . '/application/create')
            ->assertForbidden();
    }

    public function test_store_for_exist_application(): void
    {
        $uuid = $this->makeVacancyUuid();
        $user = User::factory()->create();

        VacancyApplication::factory(['user_id' => $user->id, 'vacancy_id' => $uuid])
            ->create();


        $this->actingAs($user)
            ->post('/vacancies/' . $uuid . '/application', ['expect_salary' => 100])
            ->assertForbidden();
    }

    /**
     * @dataProvider dataForValidation
     */
    public function test_create_form_for_user_validation(array $data, array $invalid, array $valid): void
    {
        $uuid = $this->makeVacancyUuid();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/vacancies/' . $uuid . '/application/create')
            ->post('/vacancies/' . $uuid . '/application', $data);

        $databaseSet = [
            'vacancy_id' => $uuid,
            'user_id' => $user->id,
        ];

        if ($invalid) {
            $response->assertInvalid($invalid)
                ->assertRedirect();
            $this->assertDatabaseMissing(VacancyApplication::class, $databaseSet);
        }

        if ($valid) {
            $response->assertValid($valid)
                ->assertRedirect('/vacancies/' . $uuid);
            $this->assertDatabaseHas(VacancyApplication::class, [...$databaseSet, ...$data]);
        }
    }
}
