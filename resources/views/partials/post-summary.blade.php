@php($cover = $content->coverImage->first())
<article class="content-summary {{ $cover ? 'content-summary--has-cover' : '' }}">
    @if ($cover)
        <a class="content-summary__cover" href="{{ $link }}" aria-label="Open {{ $content->title }}">
            <img class="content-summary__cover-image" src="{{ $cover->getThumbnailUrl() }}"
                alt="Cover image for {{ $content->title }}" loading="lazy" decoding="async" />
        </a>
    @endif
    <div class="content-summary__body {{ $cover ? 'content-summary__body--has-cover' : '' }}">
        <h2 class="content-title">
            <a class="content-link" href="{{ $link }}">{{ $content->title }}</a>
        </h2>

        @if (($showDate ?? false) && $content->created_at)
            <h4 class="content-meta">
                <time class="content-date" datetime="{{ $content->created_at->toW3cString() }}">
                    {{ $content->created_at->format('F j, Y') }}
                </time>
            </h4>
        @endif

        @if ($content->excerpt)
            <p class="content-excerpt">{{ $content->excerpt }}</p>
        @endif
        <a class="content-read-more" href="{{ $link }}">Read More</a>
    </div>
</article>
