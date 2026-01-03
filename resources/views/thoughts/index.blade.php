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
                                <input class="thoughts-attach__input" id="attachment-new" type="file" accept="image/*"
                                    multiple data-embed-input />
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
                    <li class="thoughts-item" data-thought-slug="{{ $content->slug }}" data-thought-item>
                        <header class="thoughts-item__header">
                            <time class="thoughts-item__time" datetime="{{ $content->created_at?->toW3cString() }}">
                                {{ $content->created_at?->format('M j, Y g:i A') }}
                            </time>

                            @auth
                                <details class="thoughts-actions" data-thought-actions>
                                    <summary class="thoughts-actions__trigger" aria-label="Actions">
                                        <x-heroicon-o-ellipsis-horizontal aria-hidden="true" focusable="false"
                                            width="18" height="18" />
                                    </summary>

                                    <div class="thoughts-actions__menu" role="menu">
                                        <a class="thoughts-actions__item" href="#" role="menuitem"
                                            data-thought-edit-link>Edit</a>
                                        <a class="thoughts-actions__item" href="#" role="menuitem"
                                            data-thought-delete>Delete</a>
                                    </div>
                                </details>
                            @endauth
                        </header>

                        <div data-thought-view>
                            <div class="thoughts-item__content">
                                {!! nl2br(e($content->body ?? '')) !!}
                            </div>

                            @if ($content->embedImages->isNotEmpty())
                                <div class="embed-gallery">
                                    @foreach ($content->embedImages as $image)
                                        <a class="embed-thumb" href="{{ $image->getUrl() }}" target="_blank"
                                            rel="noopener">
                                            <img src="{{ $image->getThumbnailUrl() }}" alt="" loading="lazy"
                                                decoding="async" />
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @auth
                            <div data-thought-edit-panel hidden>
                                <x-form action="{{ route('thoughts.update', ['slug' => $content->slug]) }}" method="PUT">
                                    <div class="form-field">
                                        <textarea class="form-textarea" id="body-{{ $content->slug }}" name="body" required data-embed-paste-target>{{ $content->body ?? '' }}</textarea>
                                    </div>

                                    <input type="hidden" name="embeds"
                                        value="{{ $content->embedImages->map(fn($image) => $image->getShortHash())->implode(',') }}" />
                                    <div class="embed-gallery" data-embed-list></div>

                                    <div class="thoughts-composer-row">
                                        <x-visibility-radio id="visibility-{{ $content->slug }}" :selected="(string) $content->visibility->value" />
                                        <div class="thoughts-attach">
                                            <label class="btn" for="attachment-{{ $content->slug }}">
                                                <x-heroicon-o-paper-clip class="btn__icon" aria-hidden="true"
                                                    focusable="false" width="16" height="16" />
                                            </label>
                                            <input class="thoughts-attach__input" id="attachment-{{ $content->slug }}"
                                                type="file" accept="image/*" multiple data-embed-input />
                                        </div>
                                    </div>

                                    <div class="thoughts-composer-row">
                                        <button class="btn btn--primary" type="submit">Save</button>
                                        <button class="btn" type="button" data-thought-cancel>Cancel</button>
                                    </div>
                                </x-form>

                                <x-form action="{{ route('thoughts.destroy', ['slug' => $content->slug]) }}"
                                    method="DELETE" data-thought-delete-form>
                                </x-form>
                            </div>
                        @endauth
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

    @push('scripts')
        @vite('resources/js/thoughts.js')
    @endpush
</x-layout>
