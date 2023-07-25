<?php

namespace App\Observers;

use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Services\VacancyApplicationCvStorage;

class VacancyObserver
{
    public function __construct(protected VacancyApplicationCvStorage $cvStorage)
    {
    }

    /**
     * Handle the Vacancy "deleting" event.
     */
    public function deleting(Vacancy $vacancy): void
    {
        VacancyApplication::where('vacancy_id', $vacancy->id)
            ->delete();
    }

    /**
     * Handle the Vacancy "forceDeleting" event.
     */
    public function forceDeleting(Vacancy $vacancy): void
    {
        $vacancy->vacancyApplications()
            ->whereNotNull('cv_path')
            ->pluck('cv_path')
            ->each(fn(string $path) => $this->cvStorage->adapter->delete($path));
    }

    /**
     * Handle the Vacancy "restored" event.
     */
    public function restored(Vacancy $vacancy): void
    {
        VacancyApplication::withTrashed()
            ->where('vacancy_id', $vacancy->id)
            ->restore();
    }
}
