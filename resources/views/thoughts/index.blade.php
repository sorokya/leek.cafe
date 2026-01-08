<x-layout title="Thoughts">
    <div class="stack thoughts">
        @auth
            <section class="section thoughts-composer" aria-label="New Thought">
                <header class="section__header">
                    <h1 class="section__title">
                        <x-heroicon-o-pencil-square class="section__title-icon" aria-hidden="true" focusable="false"
                            width="24" height="24" />
                        Share a Thought
                    </h1>
                </header>
                <div class="section__content">
                    <x-form action="{{ route('thoughts.store') }}" method="POST">
                        <div class="form-field">
                            <textarea class="form-textarea" id="body-new" name="body" placeholder="What's on your mind?" required></textarea>
                        </div>

                        <input type="hidden" name="embeds" id="embeds-new" value="{{ old('embeds', '') }}" />
                        <div class="embed-gallery" data-embed-list></div>

                        <div class="thoughts-composer-row">
                            <x-visibility-radio id="visibility-new" :selected="(string) \App\Visibility::PRIVATE->value" />
                            <div class="thoughts-attach">
                                <label class="btn" for="attachment-new">
                                    <x-heroicon-o-paper-clip class="btn__icon" aria-hidden="true" focusable="false"
                                        width="16" height="16" />
                                </label>
                                <input class="thoughts-attach__input" id="attachment-new" type="file"
                                    accept="image/*,video/*" multiple data-embed-input />
                            </div>
                        </div>
                        <button class="btn btn--primary" type="submit">Post</button>
                    </x-form>
                </div>
            </section>
        @endauth

        <ol class="thoughts-feed" role="list">
            @if ($contents && $contents->isNotEmpty())
                @foreach ($contents as $content)
                    @include('partials.thought-feed-item', [
                        'content' => $content,
                        'asListItem' => true,
                    ])
                @endforeach
            @else
                <li class="thoughts-item">
                    <div class="thoughts-item__content">
                        <p class="content-meta">No thoughts yet. Be the first to share one!</p>
                    </div>
                </li>
            @endif
        </ol>
    </div>
</x-layout>
