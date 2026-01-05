<x-layout>
    <div class="stack">
        <section class="section" aria-label="Post feed">
            <header class="section__header">
                <h1 class="section__title">
                    <x-heroicon-c-clock class="section__title-icon" aria-hidden="true" focusable="false" width="24"
                        height="24" />
                    Recent Posts
                </h1>
            </header>

            <div class="section__content">
                <div class="content-feed">
                    @foreach ($contents as $content)
                        @include('partials.post-summary', [
                            'content' => $content,
                            'link' => route('posts.show', ['slug' => $content->slug]),
                            'showDate' => true,
                        ])
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-layout>
