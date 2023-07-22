<?php

namespace App\Observers;

use App\Models\Vacancy;
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
        $vacancy->vacancyApplications()
            ->whereNotNull('cv_path')
            ->pluck('cv_path')
            ->each(fn(string $path) => $this->cvStorage->adapter->delete($path));
    }
}
