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
     * Handle the VacancyApplication "deleted" event.
     */
    public function deleted(VacancyApplication $vacancyApplication): void
    {
        if ($path = $vacancyApplication->cv_path) {
            $this->cvStorage->adapter->delete($path);
        }
    }
}
