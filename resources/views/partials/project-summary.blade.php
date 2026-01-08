@php($cover = $content->coverImage->first())
<article class="content-summary {{ $cover ? 'content-summary--has-cover' : '' }}">
    @if ($cover)
        <a class="content-summary__cover" href="{{ $link }}" aria-label="Open {{ $content->title }}">
            <img class="content-summary__cover-image" src="{{ $cover->getThumbnailUrl() }}"
                alt="Cover image for {{ $content->title }}" loading="lazy" decoding="async" />
        </a>
    @endif

    <div class="content-summary__body {{ $cover ? 'content-summary__body--has-cover' : '' }}">
        <div class="content-title-row">
            <h2 class="content-title content-title-row__title">
                <a class="content-link" href="{{ $link }}">{{ $content->title }}</a>
            </h2>

            <x-visibility-pill :content="$content" class="content-title-row__pill" />
        </div>

        @if ($content->project?->url)
            <p class="content-meta">
                <a href="{{ $content->project->url }}" target="_blank" rel="noopener noreferrer">
                    {{ $content->project->url }}
                </a>
            </p>
        @endif

        @if ($content->excerpt)
            <p class="content-excerpt">{{ $content->excerpt }}</p>
        @endif

        <a class="content-read-more" href="{{ $link }}">Read More</a>
    </div>
</article>
