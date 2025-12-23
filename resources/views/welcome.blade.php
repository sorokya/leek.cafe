<x-layout>
    <div class="stack">
        <section class="section" aria-label="Post feed">
            <header class="section__header">
                <h1 class="section__title">
                    Recent Posts
                    <a class="section__title-link" href="#" aria-label="RSS Feed">
                        <x-heroicon-s-rss aria-hidden="true" focusable="false" width="24" height="24" />
                    </a>
                </h1>
            </header>

            <div class="section__content">
                <div class="post-feed">
                    @foreach ($posts as $post)
                        <article class="post-summary">
                            <h2 class="post-title">
                                <a class="post-link" href="{{ $post->link() }}">{{ $post->title }}</a>
                            </h2>
                            <h4 class="post-meta">
                                <time class="post-date" datetime="{{ $post->created_at->toW3cString() }}">
                                    {{ $post->created_at->format('F j, Y') }}
                                </time>
                            </h4>
                            <p class="post-excerpt">{{ $post->excerpt() }}</p>
                            <a class="post-read-more" href="{{ $post->link() }}">Read More</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-layout>
