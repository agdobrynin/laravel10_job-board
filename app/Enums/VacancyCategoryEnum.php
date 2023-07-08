<?php

namespace App\Enums;

use App\Traits\MakeAndValuesTrait;

enum VacancyCategoryEnum: string
{
    use MakeAndValuesTrait;

    case IT = 'it';
    case FINANCE = 'finance';
    case SALES = 'sales';
    case MARKETING = 'marketing';
}

