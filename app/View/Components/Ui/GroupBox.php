<?php

namespace App\View\Components\Ui;

use App\Contracts\GroupBoxEnumInterface;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GroupBox extends Component
{
    /** @var GroupBoxEnumInterface[] */
    public readonly array $options;

    public function __construct(
        public readonly string $legend,
        public readonly string $name,
        public readonly string $value,
        array                  $options,
        public readonly bool   $withAll = true,
        public readonly bool   $required = false,
    )
    {
        $this->options = array_map(function ($option) {
            if (!($option instanceof GroupBoxEnumInterface)) {
                throw new \RuntimeException('Options must be implement ' . GroupBoxEnumInterface::class);
            }

            return $option;
        }, $options);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.group-box');
    }
}
