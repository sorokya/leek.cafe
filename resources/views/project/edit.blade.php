<x-layout title="Edit: {{ $content->title }}">
    <x-form-card title="Edit Project" description="Edit the details of your project below."
        action="{{ route('projects.update', $content->slug) }}" method="PUT" encType="multipart/form-data" class="wide">
        <x-slot name="fields">
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-input"
                    value="{{ old('title', $content->title) }}" required>
            </div>
            <div class="form-group">
                <label for="url" class="form-label">Project URL</label>
                <input type="url" id="url" name="url" class="form-input"
                    value="{{ old('url', $content->project?->url) }}" required
                    placeholder="https://github.com/username/repo" />
            </div>
            <div class="form-group">
                <label for="cover" class="form-label">Cover Image</label>
                @if ($content->coverImage->first())
                    <img src="{{ $content->coverImage->first()->getUrl() }}" alt="Current Cover Image"
                        class="cover-image-preview" />
                @endif
                <input type="file" id="cover" name="cover" accept="image/*"
                    @error('cover')
                    aria-invalid="true" aria-describedby="cover-error" @enderror
                    @class(['form-input', 'form-input--invalid' => $errors->has('cover')]) />
                @error('cover')
                    <p class="form-hint form-hint--error" id="cover-error">{{ $message }}</p>
                @enderror
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
                Update Project
            </button>
            <a href="{{ route('projects.show', $content->slug) }}" class="btn btn-secondary">
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
