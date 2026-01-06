<x-layout title="Posts">
    <div class="stack">
        <section class="section" aria-label="Post feed">
            <header class="section__header">
                <h1 class="section__title">
                    <x-heroicon-o-newspaper class="section__title-icon" aria-hidden="true" focusable="false" width="24"
                        height="24" />
                    Posts
                </h1>
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
                <div class="content-feed">
                    @foreach ($contents as $content)
                        @include('partials.post-summary', [
                            'content' => $content,
                            'link' => route('posts.show', ['slug' => $content->slug]),
                            'showDate' => true,
                        ])
                    @endforeach
                    <div class="pagination-links">
                        {!! $contents->links() !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
