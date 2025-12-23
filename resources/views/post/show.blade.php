<x-layout title="{{ $post->title }}">
    <article class="post-detail">
        <header class="post-detail__header">
            <h1 class="post-detail__title">{{ $post->title }}</h1>
            <h4 class="post-detail__meta">
                <time class="post-detail__date" datetime="{{ $post->published_at?->toW3cString() }}">
                    {{ $post->published_at?->format('F j, Y') }}
                </time>
            </h4>
        </header>

        <div class="post-detail__content">
            {!! $post->render() !!}
        </div>
    </article>
</x-layout>
