<form {{ $attributes->merge(['class' => 'form']) }} method="post" action="{{ $action }}"
    enctype="{{ $encType }}">
    @csrf

    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    {{ $slot }}
</form>
