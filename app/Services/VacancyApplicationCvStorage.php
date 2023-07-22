<?php

namespace App\Services;

use Illuminate\Filesystem\FilesystemAdapter;

readonly class VacancyApplicationCvStorage
{
    public function __construct(public FilesystemAdapter $adapter)
    {
    }
}
