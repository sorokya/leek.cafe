<x-layout title="{{ $content->title }}">
    <article class="post-detail">
        <header class="post-detail__header">
            <h1 class="post-detail__title">
                {{ $content->title }}
            </h1>
            <h4 class="post-detail__meta">
                <time class="post-detail__date" datetime="{{ $published_at?->toW3cString() }}">
                    {{ $published_at?->format('F j, Y') }}
                </time>
            </h4>
            @auth
                <section class="post-detail__actions">
                    <a href={{ route('posts.edit', $content->slug) }} class="btn btn--primary btn--small">
                        <x-heroicon-o-pencil-square class="btn__icon" aria-hidden="true" focusable="false" width="16"
                            height="16" />
                        Edit
                    </a>
                    <a href={{ route('posts.delete-confirm', $content->slug) }} class="btn btn--danger btn--small">
                        <x-heroicon-o-trash class="btn__icon" aria-hidden="true" focusable="false" width="16"
                            height="16" />
                        Delete
                    </a>
                </section>
            @endauth
        </header>

        <div class="post-detail__content">
            {!! $renderedBody !!}
        </div>
    </article>
</x-layout>
