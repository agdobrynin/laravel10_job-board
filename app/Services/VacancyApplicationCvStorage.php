<?php

namespace App\Services;

use App\Contracts\VacancyApplicationCvStorageInterface;
use Illuminate\Filesystem\FilesystemAdapter;

readonly class VacancyApplicationCvStorage implements VacancyApplicationCvStorageInterface
{
    public function __construct(protected FilesystemAdapter $adapter)
    {
    }

    public function adapter(): FilesystemAdapter
    {
        return $this->adapter;
    }
}
