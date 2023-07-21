<?php

namespace App\View\Components\Vacancy;

use App\Models\Vacancy;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        readonly public ?Vacancy $vacancy,
        readonly public string $action,
        readonly public string $method,
        readonly public string $buttonTitle,
    )
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.vacancy.form');
    }
}
