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
        User::factory(10)
            ->has(
                Employer::factory(2)
            )
            ->create();

        Employer::all()
            ->each(function (Employer $employer) {
                $employer->vacancies()
                    ->saveMany(
                        Vacancy::factory(rand(0, 20))->make()
                    );
            });
    }
}
