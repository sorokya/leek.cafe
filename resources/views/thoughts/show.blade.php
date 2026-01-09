<x-layout title="Thought">
    <a href="{{ route('thoughts.index') }}" class="btn btn--small">
        <x-heroicon-o-arrow-left class="btn__icon" aria-hidden="true" focusable="false" width="16" height="16" />
        Back to Thoughts
    </a>
    <div class="stack thoughts">
        @include('thoughts._item', ['content' => $content, 'wrapContent' => true, 'shortSlug' => false])
    </div>

</x-layout>
