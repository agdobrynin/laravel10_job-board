<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Notifications\OfferFromEmployee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TestHelper;

class VacancyApplicationControllerTest extends TestCase
{
    use RefreshDatabase;

    public static function dataForValidation(): \Generator
    {
        $file = UploadedFile::fake()->create('abc.pdf', 1);

        yield 'salary expect required' => [
            [],
            ['expect_salary' => 'required', 'cv' => 'required'],
            [],
        ];

        yield 'salary expect must be integer' => [
            ['expect_salary' => 'abc'],
            ['expect_salary' => 'integer'],
            [],
        ];

        yield 'cv max size 2mb' => [
            ['cv' => UploadedFile::fake()->create('abc.pdf', 2100)],
            ['cv' => '2048'],
            [],
        ];

        yield 'cv must be file' => [
            ['cv' => 'abc', 'expect_salary' => 100],
            ['cv' => 'file'],
            [],
        ];

        yield 'cv must be mime format  pdf,docx,odt,txt' => [
            ['cv' => UploadedFile::fake()->image('aaa'), 'expect_salary' => 100],
            ['cv' => 'pdf, docx, doc, odt, txt'],
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
            ['expect_salary' => 10_000, 'cv' => $file],
            [],
            ['expect_salary']
        ];

        yield 'salary success if input as string with spaces' => [
            ['expect_salary' => ' 10000 ', 'cv' => $file],
            [],
            ['expect_salary']
        ];

        yield 'salary must be less or equal then 1 million' => [
            ['expect_salary' => 1_000_000, 'cv' => $file],
            [],
            ['expect_salary'],
        ];

        yield 'salary 10 million' => [
            ['expect_salary' => 1_000_001, 'cv' => $file],
            ['expect_salary'],
            []
        ];
    }

    public function test_create_form_unauthorized_user(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);

        $this->get('/vacancies/' . $uuid . '/application/create')
            ->assertRedirect('/login');
    }

    public function test_create_form_for_user(): void
    {
        $uuid = Str::uuid();
        $user = TestHelper::makeVacancyWithApplication($uuid);
        $vacancy = Vacancy::find($uuid);

        $url = Config::get('app.url');

        $this->actingAs($user)
            ->get('/vacancies/' . $uuid . '/application/create')
            ->assertOk()
            ->assertSeeInOrder([
                '>' . $vacancy->title . '</h2>',
                '>Salary: $' . number_format($vacancy->salary) . '</div>',
                'Your Job Application',
                'action="' . $url . '/vacancies/' . $uuid . '/application"',
                '>Expected Salary</label>',
                'name="expect_salary"'
            ], false);
    }

    public function test_create_form_for_exist_application(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);
        $user = User::factory()
            ->has(VacancyApplication::factory(['vacancy_id' => $uuid]))
            ->create();


        $this->actingAs($user)
            ->get('/vacancies/' . $uuid . '/application/create')
            ->assertForbidden();
    }

    public function test_create_form_for_unverified_user(): void
    {
        $uuid = Str::uuid();
        $user = TestHelper::makeVacancyWithApplication($uuid);

        $user->email_verified_at = null;
        $user->save();

        $this->actingAs($user)
            ->get('/vacancies/' . $uuid . '/application/create')
            ->assertRedirect('/email/verify');
    }

    public function test_store_for_exist_application(): void
    {
        $uuid = Str::uuid();
        TestHelper::makeVacancyWithApplication($uuid);
        $user = User::factory()
            ->has(VacancyApplication::factory(['vacancy_id' => $uuid]))
            ->create();

        $this->actingAs($user)
            ->post(
                '/vacancies/' . $uuid . '/application',
                [
                    'expect_salary' => 100,
                    'cv' => UploadedFile::fake()->create('aaa.txt', 1)
                ])
            ->assertForbidden();
    }

    /**
     * @dataProvider dataForValidation
     */
    public function test_create_form_for_user_validation(array $data, array $invalid, array $valid): void
    {
        Notification::fake();
        Storage::fake('cv');

        $uuid = Str::uuid();
        $user = TestHelper::makeVacancyWithApplication($uuid);

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
            if ($data['cv']) {
                $data['cv_path'] = $data['cv']->hashName();
                unset($data['cv']);
                Storage::disk('cv')->assertExists($data['cv_path']);
            }

            $response->assertValid($valid)
                ->assertRedirect('/vacancies/' . $uuid);
            $this->assertDatabaseHas(VacancyApplication::class, [...$databaseSet, ...$data]);

            Notification::assertSentTo(
                [Vacancy::find($uuid)->employer->user],
                OfferFromEmployee::class
            );
        }
    }
}
