<x-layout title="Create New Post">
    <x-form-card title="Create New Post" description="Enter the details of your new post below."
        action="{{ route('posts.store') }}" method="POST" class="wide">
        <x-slot name="fields">
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}"
                    required>
            </div>
            <div class="form-group">
                <label for="visibility" class="form-label">Visibility</label>
                <x-visibility-select :selected="old('visibility')" />
            </div>
            <div class="form-group">
                <label for="body" class="form-label">Body (Markdown)</label>
                <input type="hidden" id="body" name="body" required value="{{ old('body') }}" />
                <div id="body-editor"></div>
            </div>
        </x-slot>
        <x-slot name="actions">
            <button type="submit" class="btn btn--primary">Create Post</button>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
        </x-slot>
    </x-form-card>

    @push('scripts')
        @vite('resources/js/edit-post.js')
    @endpush
</x-layout>
