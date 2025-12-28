<x-layout title="Projects">
    <div class="stack">
        <section class="section" aria-label="Project list">
            <header class="section__header">
                <h1 class="section__title">
                    <x-heroicon-o-code-bracket class="section__title-icon" aria-hidden="true" focusable="false"
                        width="24" height="24" />
                    Projects
                </h1>
            </header>

            <div class="section__content">
                @auth
                    <div class="section__actions">
                        <a href="{{ route('projects.create') }}" class="btn btn--success btn--small">
                            <x-heroicon-c-plus class="btn__icon" aria-hidden="true" focusable="false" width="16"
                                height="16" />
                            New Project
                        </a>
                    </div>
                @endauth

                @if ($contents->isEmpty())
                    <p class="content-meta">
                        No projects yet. If there were projects, they'd be listed here!
                    </p>
                @else
                    <div class="content-feed">
                        @foreach ($contents as $content)
                            @php($cover = $content->coverImage->first())
                            @php($link = route('projects.show', ['slug' => $content->slug]))

                            <article class="content-summary {{ $cover ? 'content-summary--has-cover' : '' }}">
                                @if ($cover)
                                    <a class="content-summary__cover" href="{{ $link }}"
                                        aria-label="Open {{ $content->title }}">
                                        <img class="content-summary__cover-image" src="{{ $cover->getThumbnailUrl() }}"
                                            alt="Cover image for {{ $content->title }}" loading="lazy"
                                            decoding="async" />
                                    </a>
                                @endif

                                <div
                                    class="content-summary__body {{ $cover ? 'content-summary__body--has-cover' : '' }}">
                                    <h2 class="content-title">
                                        <a class="content-link" href="{{ $link }}">{{ $content->title }}</a>
                                    </h2>

                                    @if ($content->project?->url)
                                        <p class="content-meta">
                                            <a href="{{ $content->project->url }}" target="_blank"
                                                rel="noopener noreferrer">
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
                        @endforeach

                        <div class="pagination-links">
                            {!! $contents->links() !!}
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-layout>
