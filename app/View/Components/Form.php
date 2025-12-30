<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Form extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public string $method = 'POST',
        public string $encType = 'application/x-www-form-urlencoded',
        public ?string $action = null,
        public ?string $fields = null,
        public ?string $actions = null,
        public ?string $class = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.form');
    }
}
