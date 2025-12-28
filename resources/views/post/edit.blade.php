<x-layout title="Edit: {{ $content->title }}">
    <x-form-card title="Edit Post" description="Edit the details of your post below."
        action="{{ route('posts.update', $content->slug) }}" method="PUT" class="wide">
        <x-slot name="fields">
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-input"
                    value="{{ old('title', $content->title) }}" required>
            </div>
            <div class="form-group">
                <label for="visibility" class="form-label">Visibility</label>
                <x-visibility-select :selected="old('visibility', $content->visibility)" />
            </div>
            <div class="form-group">
                <label for="body" class="form-label">Body (Markdown)</label>
                <input type="hidden" id="body" name="body" required
                    value="{{ old('body', $content->body) }}" />
                <div id="body-editor"></div>
            </div>
        </x-slot>
        <x-slot name="actions">
            <button type="submit" class="btn btn--primary">
                <x-heroicon-c-check class="btn__icon" aria-hidden="true" focusable="false" width="16"
                    height="16" />
                Update Post
            </button>
            <a href="{{ route('posts.show', $content->slug) }}" class="btn btn-secondary">
                <x-heroicon-c-arrow-uturn-left class="btn__icon" aria-hidden="true" focusable="false" width="16"
                    height="16" />
                Cancel
            </a>
        </x-slot>
    </x-form-card>

    @push('scripts')
        @vite('resources/js/edit-post.js')
    @endpush
</x-layout>
