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
    $slotContent = trim((string) $slot);
    $hasSlot = $slotContent !== '';

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

    $providedStyle = $attributes->get('style');

    if (is_string($providedStyle) && $providedStyle !== '') {
        $style .= rtrim($providedStyle, ';') . ';';
    }

    if (is_string($bg) && $bg !== '') {
        $style .= '--status-pill-bg: ' . $bg . ';';
    }

    if (is_string($fg) && $fg !== '') {
        $style .= '--status-pill-fg: ' . $fg . ';';
    }
@endphp

<span {{ $attributes->merge(['class' => 'status-pill'])->except('style') }}
    @if ($style !== '') style="{{ $style }}" @endif>
    @if ($icon)
        @if ($iconComponent)
            <x-dynamic-component :component="$iconComponent" class="status-pill__icon" width="16" height="16"
                aria-hidden="true" focusable="false" />
        @else
            <span class="status-pill__icon" aria-hidden="true">{{ $icon }}</span>
        @endif
    @endif
    <span class="status-pill__label">
        @if ($hasSlot)
            {{ $slot }}@else{{ $label }}
        @endif
    </span>
</span>
