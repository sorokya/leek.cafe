<x-layout>
    <div class="stack">
        <section class="section" aria-label="Post feed">
            <header class="section__header">
                <h1 class="section__title">
                    <x-heroicon-c-clock class="section__title-icon" aria-hidden="true" focusable="false" width="24"
                        height="24" />
                    Recent Posts
                </h1>
            </header>

            <div class="section__content">
                <div class="content-feed">
                    @foreach ($contents as $content)
                        @php($cover = $content->coverImage->first())
                        @php($link = route('posts.show', ['slug' => $content->slug]))
                        <article class="content-summary {{ $cover ? 'content-summary--has-cover' : '' }}">
                            @if ($cover)
                                <a class="content-summary__cover" href="{{ $link }}"
                                    aria-label="Open {{ $content->title }}">
                                    <img class="content-summary__cover-image" src="{{ $cover->getThumbnailUrl() }}"
                                        alt="Cover image for {{ $content->title }}" loading="lazy" decoding="async" />
                                </a>
                            @endif
                            <div class="content-summary__body {{ $cover ? 'content-summary__body--has-cover' : '' }}">
                                <h2 class="content-title">
                                    <a class="content-link" href="{{ $link }}">{{ $content->title }}</a>
                                </h2>
                                <h4 class="content-meta">
                                    <time class="content-date" datetime="{{ $content->created_at?->toW3cString() }}">
                                        {{ $content->created_at?->format('F j, Y') }}
                                    </time>
                                </h4>
                                @if ($content->excerpt)
                                    <p class="content-excerpt">{{ $content->excerpt }}</p>
                                @endif
                                <a class="content-read-more" href="{{ $link }}">Read More</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-layout>
