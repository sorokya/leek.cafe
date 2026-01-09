@php
    /** @var \App\Models\Content $content */
    /** @var bool $wrapContent */
    $wrapContent = $wrapContent ?? false;
@endphp

<div class="thoughts-item__content">
    @if ($wrapContent)
        <div class="content">
            {!! $content->rendered !!}
        </div>
    @else
        {!! $content->rendered !!}
    @endif

    @if ($content->embedImages->isNotEmpty())
        <div class="embed-gallery">
            @foreach ($content->embedImages as $image)
                <div class="embed-item">
                    <a class="embed-thumb" href="{{ $image->getUrl() }}" target="_blank" rel="noopener"
                        data-embed-kind="{{ strtolower((string) $image->extension) === 'mp4' ? 'video' : 'image' }}">
                        <img src="{{ $image->getThumbnailUrl() }}" alt="" loading="lazy" decoding="async" />

                        @if (strtolower((string) $image->extension) === 'mp4')
                            <span class="embed-play" aria-hidden="true">
                                <span class="embed-play__icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path
                                            d="M8 5.5v13a1 1 0 0 0 1.52.85l10-6.5a1 1 0 0 0 0-1.7l-10-6.5A1 1 0 0 0 8 5.5Z" />
                                    </svg>
                                </span>
                            </span>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
