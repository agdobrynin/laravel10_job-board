<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait LabelEnum
{
    public function label(): string
    {
        return Str::ucfirst($this->value);
    }
}
