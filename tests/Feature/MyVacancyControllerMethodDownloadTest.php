<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\User;
use App\Models\VacancyApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\TestHelper;

class MyVacancyControllerMethodDownloadTest extends TestCase
{
    use RefreshDatabase;

    public static function downloadBy(): \Generator
    {
        $vacancyUuid = Str::uuid()->toString();
        $applicationUuid = Str::uuid()->toString();

        $makeCvFile = function () {
            $file = UploadedFile::fake()->create('abc.pdf', 1);

            return Storage::fake('cv')->putFile($file);
        };

        yield 'anonymous' => [
            null,
            $vacancyUuid,
            $applicationUuid,
            302,
            '/login',
        ];

        yield 'by regular user' => [
            function () use ($vacancyUuid, $applicationUuid) {
                TestHelper::makeVacancyWithApplication($vacancyUuid);

                return User::factory()->has(
                    VacancyApplication::factory(['id' => $applicationUuid, 'vacancy_id' => $vacancyUuid])
                )->create();
            },
            $vacancyUuid,
            $applicationUuid,
            403,
            null,
        ];

        yield 'by regular other employer' => [
            function () use ($vacancyUuid, $applicationUuid) {
                TestHelper::makeVacancyWithApplication($vacancyUuid, $applicationUuid);

                return User::factory()->has(Employer::factory())->create();
            },
            $vacancyUuid,
            $applicationUuid,
            403,
            null,
        ];

        yield 'by employer owner vacancy but without cover letter file' => [
            fn() => TestHelper::makeVacancyWithApplication($vacancyUuid, $applicationUuid),
            $vacancyUuid,
            $applicationUuid,
            404,
            null,
        ];

        yield 'by employer owner vacancy with cover letter file' => [
            function () use ($vacancyUuid, $applicationUuid, $makeCvFile) {
                $user = TestHelper::makeVacancyWithApplication($vacancyUuid, $applicationUuid);
                VacancyApplication::find($applicationUuid)->update(['cv_path' => $makeCvFile()]);

                return $user;
            },
            $vacancyUuid,
            $applicationUuid,
            200,
            null,
        ];

        yield 'by employer owner vacancy with cover letter file in database but without file system' => [
            function () use ($vacancyUuid, $applicationUuid) {
                $user = TestHelper::makeVacancyWithApplication($vacancyUuid, $applicationUuid);
                VacancyApplication::find($applicationUuid)->update(['cv_path' => 'cover-letter.pdf']);

                return $user;
            },
            $vacancyUuid,
            $applicationUuid,
            404,
            null,
        ];

        yield 'by employer vacancy but application from other vacancy' => [
            function () use ($vacancyUuid, $applicationUuid) {
                $user = TestHelper::makeVacancyWithApplication($vacancyUuid);

                TestHelper::makeVacancyWithApplication(Str::uuid()->toString(), $applicationUuid);

                return $user;
            },
            $vacancyUuid,
            $applicationUuid,
            404,
            null,
        ];
    }

    /** @dataProvider downloadBy */
    public function test_download_by(
        ?\Closure $initUser,
        string    $vacancyUUid,
        string    $applicationUUid,
        int       $statusCode,
        ?string   $redirectTo,
    ): void
    {
        Storage::fake('cv');

        if ($initUser && $actingAs = $initUser()) {
            $this->actingAs($actingAs);
        }

        $response = $this->get("/my-vacancy/{$vacancyUUid}/download/{$applicationUUid}")
            ->assertStatus($statusCode);

        if ($redirectTo) {
            $response->assertRedirect($redirectTo);
        }
    }
}
