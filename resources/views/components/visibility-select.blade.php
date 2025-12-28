@php
    $publicValue = (string) \App\Visibility::PUBLIC->value;
    $privateValue = (string) \App\Visibility::PRIVATE->value;
    $unlistedValue = (string) \App\Visibility::UNLISTED->value;
@endphp

<select class="form-input" id="visibility" name="visibility" required>
    <option value="{{ $publicValue }}" {{ $selected === $publicValue ? 'selected' : '' }}>
        Public
    </option>
    <option value="{{ $privateValue }}" {{ $selected === $privateValue ? 'selected' : '' }}>
        Private
    </option>
    <option value="{{ $unlistedValue }}" {{ $selected === $unlistedValue ? 'selected' : '' }}>
        Unlisted</option>
</select>
