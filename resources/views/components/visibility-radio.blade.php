@props([
    'selected' => (string) \App\Visibility::PRIVATE->value,
    'name' => 'visibility',
    'id' => 'visibility',
    'ariaLabel' => 'Visibility',
])

@php
    $publicValue = (string) \App\Visibility::PUBLIC->value;
    $privateValue = (string) \App\Visibility::PRIVATE->value;
    $unlistedValue = (string) \App\Visibility::UNLISTED->value;
@endphp

<fieldset {{ $attributes->merge(['class' => 'toggle-group']) }} aria-label="{{ $ariaLabel }}">
    <div class="toggle-group__inner" role="radiogroup" aria-label="{{ $ariaLabel }}">
        <input class="toggle-group__input" type="radio" name="{{ $name }}" id="{{ $id }}-public"
            value="{{ $publicValue }}" @checked($selected === $publicValue) required />
        <label class="toggle-group__button" for="{{ $id }}-public">Public</label>

        <input class="toggle-group__input" type="radio" name="{{ $name }}" id="{{ $id }}-private"
            value="{{ $privateValue }}" @checked($selected === $privateValue) required />
        <label class="toggle-group__button" for="{{ $id }}-private">Private</label>

        <input class="toggle-group__input" type="radio" name="{{ $name }}" id="{{ $id }}-unlisted"
            value="{{ $unlistedValue }}" @checked($selected === $unlistedValue) required />
        <label class="toggle-group__button" for="{{ $id }}-unlisted">Unlisted</label>
    </div>
</fieldset>
