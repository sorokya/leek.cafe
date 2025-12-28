<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public string $ogType = 'website',
        public ?string $url = null,
        public ?string $theme = null,
        public string $siteName = 'Leek Cafe',
        public string $tagline = 'Software engineer — 10+ years building cool projects',
        public string $logoHref = '/',
        public string $logoSrc = '/img/apple-touch-icon.png',
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.layout');
    }
}
