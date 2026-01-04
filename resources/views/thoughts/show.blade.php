<x-layout title="Thought">
    <div class="stack thoughts">
        <article class="thoughts-item">
            <header class="thoughts-item__header">
                <a class="thoughts-item__time" href="{{ route('thoughts.show', ['slug' => $content->slug]) }}">
                    <time datetime="{{ $published_at?->toW3cString() }}">
                        {{ $published_at?->format('M j, Y g:i A') }}
                    </time>
                </a>
            </header>

            <div class="thoughts-item__content content">
                {!! $renderedBody !!}
            </div>

            @if ($content->embedImages->isNotEmpty())
                <div class="embed-gallery">
                    @foreach ($content->embedImages as $image)
                        <a class="embed-thumb" href="{{ $image->getUrl() }}" target="_blank" rel="noopener">
                            <img src="{{ $image->getThumbnailUrl() }}" alt="" loading="lazy" decoding="async" />
                        </a>
                    @endforeach
                </div>
            @endif
        </article>
    </div>
</x-layout>
