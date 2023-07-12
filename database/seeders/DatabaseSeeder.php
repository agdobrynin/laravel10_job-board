<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Employer;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Employer::factory(15)
            ->for(
                User::factory()->create()
            )
            ->create()
            ->each(function (Employer $employer) {
                $employer->vacancies()
                    ->saveMany(
                        Vacancy::factory(rand(6, 25))->make()
                    );
            });
    }
}
