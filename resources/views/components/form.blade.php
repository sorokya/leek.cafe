@props([
    'action' => '#',
    'method' => 'POST',
])

<form {{ $attributes->merge(['class' => 'form']) }} method="post" action="{{ $action }}">
    @csrf

    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    {{ $slot }}
</form>
