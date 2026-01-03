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
                            <textarea class="form-textarea" id="body" name="body" placeholder="What's on your mind?" required></textarea>
                        </div>
                        <div class="thoughts-composer-row">
                            <x-visibility-radio :selected="(string) \App\Visibility::PRIVATE->value" />
                            <div class="thoughts-attach">
                                <label class="btn" for="attachment">
                                    <x-heroicon-o-paper-clip class="btn__icon" aria-hidden="true" focusable="false"
                                        width="16" height="16" />
                                </label>
                                <input class="thoughts-attach__input" id="attachment" type="file" />
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
                    <li class="thoughts-item" data-thought-slug="{{ $content->slug }}">
                        <header class="thoughts-item__header">
                            <time class="thoughts-item__time" datetime="{{ $content->created_at?->toW3cString() }}">
                                {{ $content->created_at?->format('M j, Y g:i A') }}
                            </time>

                            <details class="thoughts-actions">
                                <summary class="thoughts-actions__trigger" aria-label="Actions">
                                    <x-heroicon-o-ellipsis-horizontal aria-hidden="true" focusable="false"
                                        width="18" height="18" />
                                </summary>

                                <div class="thoughts-actions__menu" role="menu">
                                    <a class="thoughts-actions__item" href="#" role="menuitem">Edit</a>
                                    <a class="thoughts-actions__item" href="#" role="menuitem">Delete</a>
                                </div>
                            </details>
                        </header>

                        <div class="thoughts-item__content">
                            {!! nl2br(e($content->body ?? '')) !!}
                        </div>
                    </li>
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
