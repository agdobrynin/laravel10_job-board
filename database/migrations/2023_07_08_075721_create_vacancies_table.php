<?php

use App\Enums\VacancyCategoryEnum;
use App\Enums\VacancyExperienceEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('salary');
            $table->string('location');
            $table->enum('category', VacancyCategoryEnum::values());
            $table->enum('experience', VacancyExperienceEnum::values());

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
