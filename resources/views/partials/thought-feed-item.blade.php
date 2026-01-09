@php
    $publishedAt = $content->createdAtInCreatedTimezone();
@endphp

<article class="thoughts-item" data-thought-item data-thought-slug="{{ $content->slug }}">
    <header class="thoughts-item__header">
        <a class="thoughts-item__time" href="{{ route('thoughts.show', ['slug' => substr($content->slug, 0, 12)]) }}">
            <time datetime="{{ $publishedAt?->toW3cString() }}">
                {{ $publishedAt?->format('M j, Y g:i A') }}
            </time>
        </a>

        <div class="thoughts-item__header-right">
            <x-visibility-pill :content="$content" />

            @auth
                <details class="thoughts-actions" data-thought-actions>
                    <summary class="thoughts-actions__trigger" aria-label="Actions">
                        <x-heroicon-o-ellipsis-horizontal aria-hidden="true" focusable="false" width="18"
                            height="18" />
                    </summary>

                    <div class="thoughts-actions__menu" role="menu">
                        <a class="thoughts-actions__item" href="#" role="menuitem" data-thought-edit-link>
                            Edit
                        </a>
                        <a class="thoughts-actions__item" href="#" role="menuitem" data-thought-delete>
                            Delete
                        </a>
                    </div>
                </details>
            @endauth
        </div>
    </header>

    <div data-thought-view>
        <div class="thoughts-item__content">
            {!! $content->rendered !!}

            @if ($content->embedImages->isNotEmpty())
                <div class="embed-gallery">
                    @foreach ($content->embedImages as $image)
                        <div class="embed-item">
                            <a class="embed-thumb" href="{{ $image->getUrl() }}" target="_blank" rel="noopener"
                                data-embed-kind="{{ strtolower((string) $image->extension) === 'mp4' ? 'video' : 'image' }}">
                                <img src="{{ $image->getThumbnailUrl() }}" alt="" loading="lazy"
                                    decoding="async" />

                                @if (strtolower((string) $image->extension) === 'mp4')
                                    <span class="embed-play" aria-hidden="true">
                                        <span class="embed-play__icon">
                                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                                <path
                                                    d="M8 5.5v13a1 1 0 0 0 1.52.85l10-6.5a1 1 0 0 0 0-1.7l-10-6.5A1 1 0 0 0 8 5.5Z" />
                                            </svg>
                                        </span>
                                    </span>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
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
                            <x-heroicon-o-paper-clip class="btn__icon" aria-hidden="true" focusable="false" width="16"
                                height="16" />
                        </label>
                        <input class="thoughts-attach__input" id="attachment-{{ $content->slug }}" type="file"
                            accept="image/*,video/*" multiple data-embed-input />
                    </div>
                </div>

                <div class="thoughts-composer-row">
                    <button class="btn btn--primary" type="submit">Save</button>
                    <button class="btn" type="button" data-thought-cancel>Cancel</button>
                </div>
            </x-form>

            <x-form action="{{ route('thoughts.destroy', ['slug' => $content->slug]) }}" method="DELETE"
                data-thought-delete-form>
            </x-form>
        </div>
    @endauth
</article>
