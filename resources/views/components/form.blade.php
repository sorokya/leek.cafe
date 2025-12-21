<section class="form-card" aria-label="{{ $title ?? 'Form' }}">
    @if ($title)
        <h1 class="form-title">{{ $title }}</h1>
    @endif

    @if ($description)
        <p class="form-description">{{ $description }}</p>
    @endif

    <form class="form" method="post" action="{{ $action ?? '#' }}">
        @csrf

        {{ $fields }}

        @if ($actions)
        <div class="form-actions">
            {{ $actions }}
        </div>
        @endif
    </form>
</section>