<?php

namespace App\Enums;

use App\Traits\MakeAndValuesTrait;

enum VacancyExperienceEnum: string
{

    use MakeAndValuesTrait;

    case JUNIOR = 'junior';
    case MIDDLE = 'middle';
    case SENIOR = 'senior';
}

