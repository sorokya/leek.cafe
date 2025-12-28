<x-layout>
    <div class="stack">
        <section class="section" aria-label="Post feed">
            <header class="section__header">
                <h1 class="section__title">
                    Recent Posts
                </h1>
            </header>

            <div class="section__content">
                <div class="post-feed">
                    @foreach ($contents as $content)
                        @php($cover = $content->coverImage->first())
                        @php($link = route('posts.show', ['slug' => $content->slug]))
                        <article class="post-summary {{ $cover ? 'post-summary--has-cover' : '' }}">
                            @if ($cover)
                                <a class="post-summary__cover" href="{{ $link }}"
                                    aria-label="Open {{ $content->title }}">
                                    <img class="post-summary__cover-image" src="{{ $cover->getThumbnailUrl() }}"
                                        alt="Cover image for {{ $content->title }}" loading="lazy" decoding="async" />
                                </a>
                            @endif
                            <div class="post-summary__body {{ $cover ? 'post-summary__body--has-cover' : '' }}">
                                <h2 class="post-title">
                                    <a class="post-link" href="{{ $link }}">{{ $content->title }}</a>
                                </h2>
                                <h4 class="post-meta">
                                    <time class="post-date" datetime="{{ $content->created_at?->toW3cString() }}">
                                        {{ $content->created_at?->format('F j, Y') }}
                                    </time>
                                </h4>
                                @if ($content->excerpt)
                                    <p class="post-excerpt">{{ $content->excerpt }}</p>
                                @endif
                                <a class="post-read-more" href="{{ $link }}">Read More</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-layout>
