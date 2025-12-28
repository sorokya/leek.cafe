<x-layout title="Create New Project">
    <x-form-card title="Create New Project" description="Enter the details of your new project below."
        action="{{ route('projects.store') }}" method="POST" class="wide" encType="multipart/form-data">
        <x-slot name="fields">
            <div class="form-group">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}"
                    required>
            </div>
            <div class="form-group">
                <label for="url" class="form-label">Project URL</label>
                <input type="url" id="url" name="url" class="form-input" value="{{ old('url') }}"
                    required placeholder="https://github.com/username/repo" />
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
        </x-slot>

        <x-slot name="actions">
            <button type="submit" class="btn btn--primary">Create Project</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
        </x-slot>
    </x-form-card>

    @push('scripts')
        @vite('resources/js/edit-post.js')
    @endpush
</x-layout>
