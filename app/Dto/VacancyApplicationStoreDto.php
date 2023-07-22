<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class VacancyApplicationStoreDto
{
    public function __construct(
        public int          $expect_salary,
        public UploadedFile $cv,
    )
    {
    }
}
