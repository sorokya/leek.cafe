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
                            @include('partials.project-summary', [
                                'content' => $content,
                                'link' => route('projects.show', ['slug' => $content->slug]),
                            ])
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
