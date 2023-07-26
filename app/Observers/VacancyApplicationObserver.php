<?php

namespace App\Observers;

use App\Contracts\VacancyApplicationCvStorageInterface;
use App\Models\VacancyApplication;

class VacancyApplicationObserver
{
    public function __construct(protected VacancyApplicationCvStorageInterface $cvStorage)
    {
    }

    /**
     * Handle the VacancyApplication "forceDeleted" event.
     */
    public function forceDeleted(VacancyApplication $vacancyApplication): void
    {
        if ($path = $vacancyApplication->cv_path) {
            $this->cvStorage->adapter()->delete($path);
        }
    }
}
