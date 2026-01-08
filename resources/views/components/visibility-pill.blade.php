@props(['content'])

@php
    /** @var mixed $content */

    $viewerId = auth()->id();
    $ownerId = is_object($content) && isset($content->user_id) ? (int) $content->user_id : null;

    $isOwner = is_int($viewerId) && $viewerId > 0 && is_int($ownerId) && $ownerId > 0 && $viewerId === $ownerId;

    $visibility = is_object($content) ? $content->visibility ?? null : null;

    if (is_int($visibility)) {
        $visibility = \App\Visibility::tryFrom($visibility);
    }

    if (is_string($visibility) && is_numeric($visibility)) {
        $visibility = \App\Visibility::tryFrom((int) $visibility);
    }

    $isNonPublic = $visibility instanceof \App\Visibility && $visibility !== \App\Visibility::PUBLIC;

    [$label, $bg] = match ($visibility) {
        \App\Visibility::PRIVATE => ['Private', 'var(--danger-soft)'],
        \App\Visibility::UNLISTED => ['Unlisted', 'var(--warning-soft)'],
        default => [null, null],
    };
@endphp

@if ($isOwner && $isNonPublic && is_string($label) && $label !== '')
    <x-status-pill :status="$label" :bg="$bg" fg="var(--text)" {{ $attributes }} />
@endif
