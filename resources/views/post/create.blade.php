<x-layout title="Create New Post">
    <x-form-card title="Create New Post" description="Enter the details of your new post below."
        action="{{ route('posts.store') }}" method="POST" class="wide" encType="multipart/form-data">
        <x-slot name="fields">
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}"
                    required>
            </div>
            <div class="form-group">
                <label for="cover" class="form-label">Cover Image</label>
                <input type="file" id="cover" name="cover" class="form-input" accept="image/*">
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

            <div class="form-group">
                <label class="form-label" for="post-embeds">Embeds</label>

                <input type="hidden" name="embeds" id="embeds" value="{{ old('embeds', '') }}" />

                <div class="thoughts-attach">
                    <label class="btn" for="post-embeds">
                        <x-heroicon-o-paper-clip class="btn__icon" aria-hidden="true" focusable="false" width="16"
                            height="16" />
                        Add images
                    </label>
                    <input class="thoughts-attach__input" id="post-embeds" type="file" accept="image/*" multiple
                        data-embed-input />
                </div>

                <div class="embed-gallery" data-embed-list></div>
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
