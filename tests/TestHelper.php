<?php

namespace Tests;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Database\Factories\VacancyFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class TestHelper
{
    public static function makeVacancyWithApplication(string $vacancyUuid, string ... $applicationUuids): User
    {
        return User::factory()
            ->has(
                Employer::factory()
                    ->has(
                        Vacancy::factory(['id' => $vacancyUuid])
                            ->when($applicationUuids, function (VacancyFactory $v, array $uuids) {
                                return $v->has(
                                    VacancyApplication::factory(count($uuids))
                                        ->state(
                                            new Sequence(fn(Sequence $sequence) => ['id' => $uuids[$sequence->index]])
                                        )
                                        ->for(User::factory()->create())
                                );
                            })
                    )
            )
            ->create();
    }
}
