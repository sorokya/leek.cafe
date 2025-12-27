@props([
    'icon' => null,
    'status' => null,
    'bg' => null,
    'fg' => null,
])

@php
    $label = $status ?? '';
    $style = '';
    $iconComponent = null;

    if (is_string($icon) && preg_match('/^heroicon-[os]-[a-z0-9-]+$/', $icon) === 1) {
        $iconComponent = $icon;
    }

    if (is_string($iconComponent) && $iconComponent !== '') {
        try {
            $bladeCompiler = app('blade.compiler');

            $componentTagCompiler = new \Illuminate\View\Compilers\ComponentTagCompiler(
                $bladeCompiler->getClassComponentAliases(),
                $bladeCompiler->getClassComponentNamespaces(),
                $bladeCompiler,
            );

            $componentTagCompiler->componentClass($iconComponent);
        } catch (\InvalidArgumentException) {
            $iconComponent = null;
        }
    }

    if (is_string($bg) && $bg !== '') {
        $style .= 'background: ' . $bg . ';';
    }

    if (is_string($fg) && $fg !== '') {
        $style .= 'color: ' . $fg . ';';
    }
@endphp

<span {{ $attributes->merge(['class' => 'status-pill']) }}
    @if ($style !== '') style="{{ $style }}" @endif>
    @if ($icon)
        @if ($iconComponent)
            <x-dynamic-component :component="$iconComponent" class="status-pill__icon" width="16" height="16"
                aria-hidden="true" focusable="false" />
        @else
            <span class="status-pill__icon" aria-hidden="true">{{ $icon }}</span>
        @endif
    @endif
    <span class="status-pill__label">{{ $label }}</span>
</span>
