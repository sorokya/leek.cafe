@php($cover = $content->coverImage->first())

<x-layout title="{{ $content->title }}" description="{{ $description }}" :image="$cover?->getUrl()" ogType="article">
    <a href="{{ route('posts.index') }}" class="btn btn--small">
        <x-heroicon-o-arrow-left class="btn__icon" aria-hidden="true" focusable="false" width="16" height="16" />
        Back to Posts
    </a>
    <article class="content-detail">
        <header class="content-detail__header">
            @if ($cover)
                <div class="content-detail__cover">
                    <img class="content-detail__cover-image" src="{{ $cover->getUrl() }}"
                        alt="Cover image for {{ $content->title }}" loading="lazy" decoding="async" />
                </div>
            @endif
            <h1 class="content-detail__title">
                {{ $content->title }}
            </h1>
            <h4 class="content-detail__meta">
                <time class="content-detail__date" datetime="{{ $published_at?->toW3cString() }}">
                    {{ $published_at?->format('F j, Y') }}
                </time>
            </h4>
            @auth
                <section class="content-detail__actions">
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

        <div class="content-detail__content">
            {!! $renderedBody !!}
        </div>
    </article>
</x-layout>
