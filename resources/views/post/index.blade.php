<x-layout title="Posts">
    <div class="stack">
        <section class="section" aria-label="Post feed">
            <header class="section__header">
                <h1 class="section__title">Posts</h1>
            </header>

            <div class="section__content">
                @auth
                    <div class="section__actions">
                        <a href="{{ route('posts.create') }}" class="btn btn--success btn--small">
                            <x-heroicon-c-plus class="btn__icon" aria-hidden="true" focusable="false" width="16"
                                height="16" />
                            New Post
                        </a>
                    </div>
                @endauth
                <div class="post-feed">
                    @foreach ($posts as $post)
                        <article class="post-summary">
                            <h2 class="post-title">
                                <a class="post-link" href="{{ $post['link'] }}">{{ $post['title'] }}</a>
                            </h2>
                            <h4 class="post-meta">
                                <time class="post-date" datetime="{{ $post['published_at']?->toW3cString() }}">
                                    {{ $post['published_at']?->format('F j, Y') }}
                                </time>
                            </h4>
                            <p class="post-excerpt">{{ $post['excerpt'] }}</p>
                            <a class="post-read-more" href="{{ $post['link'] }}">Read More</a>
                        </article>
                    @endforeach
                    <div class="pagination-links">
                        {!! $links !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
