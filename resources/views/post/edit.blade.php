<x-layout title="Edit: {{ $post->title }}">
    <x-form title="Edit Post" description="Edit the details of your post below."
        action="{{ route('posts.update', $post->slug) }}" method="PUT" class="wide">
        <x-slot name="fields">
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-input"
                    value="{{ old('title', $post->title) }}" required>
            </div>
            <div class="form-group">
                <label for="body" class="form-label">Body (Markdown)</label>
                <input type="hidden" id="body" name="body" required value="{{ old('body', $post->body) }}" />
                <div id="body-editor"></div>
            </div>
            <div class="form-group">
                <label for="published_at" class="form-label">
                    Publish Date <button id="btn-unpublish" class="btn btn--sm" type="button">Clear</button>
                </label>
                <input type="datetime-local" id="published_at" name="published_at" class="form-input"
                    value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
            </div>
        </x-slot>
        <x-slot name="actions">
            <button type="submit" class="btn btn--primary">Update Post</button>
            <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-secondary">Cancel</a>
        </x-slot>
    </x-form>

    @push('scripts')
        @vite('resources/js/edit-post.js')
    @endpush
</x-layout>
