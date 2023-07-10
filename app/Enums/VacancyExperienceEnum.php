<?php

namespace App\Enums;

use App\Contracts\GroupBoxEnumInterface;
use App\Traits\LabelEnum;
use App\Traits\MakeAndValuesTrait;

enum VacancyExperienceEnum: string implements GroupBoxEnumInterface
{

    use MakeAndValuesTrait;
    use LabelEnum;

    case JUNIOR = 'junior';
    case MIDDLE = 'middle';
    case SENIOR = 'senior';
}

