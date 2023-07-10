<?php

namespace App\Enums;

use App\Contracts\GroupBoxEnumInterface;
use App\Traits\LabelEnum;
use App\Traits\MakeAndValuesTrait;

enum VacancyCategoryEnum: string implements GroupBoxEnumInterface
{
    use MakeAndValuesTrait;
    use LabelEnum;

    case IT = 'it';
    case FINANCE = 'finance';
    case SALES = 'sales';
    case MARKETING = 'marketing';
}

