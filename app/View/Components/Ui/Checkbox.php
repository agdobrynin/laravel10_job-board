<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    public readonly string $type;

    public function __construct(
        public readonly string  $name,
        public readonly string  $label,
        public readonly ?string $value = null,
        public readonly bool    $checked = false,
        string                  $type = 'radio',
        public readonly bool    $showError = false,
        public readonly bool    $required = false,
    )
    {
        if (!\in_array(strtolower($type), ['radio', 'checkbox'])) {
            throw new \RuntimeException('Wrong value ' . $type . ' for parameters $type');
        }

        $this->type = $type;
    }

    public function render(): View|Closure|string
    {
        return view('components.ui.checkbox');
    }
}
