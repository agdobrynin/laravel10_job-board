<?php

namespace App\Observers;

use App\Models\VacancyApplication;
use App\Services\VacancyApplicationCvStorage;

class VacancyApplicationObserver
{
    public function __construct(protected VacancyApplicationCvStorage $cvStorage)
    {
    }

    /**
     * Handle the VacancyApplication "forceDeleted" event.
     */
    public function forceDeleted(VacancyApplication $vacancyApplication): void
    {
        if ($path = $vacancyApplication->cv_path) {
            $this->cvStorage->adapter->delete($path);
        }
    }
}
