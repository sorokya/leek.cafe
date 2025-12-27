@props([
    'title' => null,
    'description' => null,
    'class' => '',
    'ariaLabel' => null,
])

<section class="form-card {{ $class }}" aria-label="{{ $ariaLabel ?? ($title ?? 'Card') }}">
    @if ($title)
        <h1 class="form-title">{{ $title }}</h1>
    @endif

    @if ($description)
        <p class="form-description">{{ $description }}</p>
    @endif

    {{ $slot }}
</section>
