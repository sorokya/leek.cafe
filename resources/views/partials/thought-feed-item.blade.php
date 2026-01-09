@php
    $publishedAt = $content->createdAtInCreatedTimezone();
@endphp

<article class="thoughts-item" data-thought-item data-thought-slug="{{ $content->slug }}"
    data-thought-edit-fragment-url="{{ route('thoughts.fragments.edit', ['slug' => $content->slug]) }}"
    data-thought-view-fragment-url="{{ route('thoughts.fragments.view', ['slug' => $content->slug]) }}">
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
        @include('thoughts._view', ['content' => $content, 'wrapContent' => false])
    </div>

    @auth
        <div data-thought-edit hidden></div>

        <x-form action="{{ route('thoughts.destroy', ['slug' => $content->slug]) }}" method="DELETE"
            data-thought-delete-form>
        </x-form>
    @endauth
</article>
