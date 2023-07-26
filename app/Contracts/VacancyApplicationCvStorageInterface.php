<?php

namespace App\Contracts;

use Illuminate\Filesystem\FilesystemAdapter;

interface VacancyApplicationCvStorageInterface
{
    public function adapter(): FilesystemAdapter;
}
