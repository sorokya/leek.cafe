<x-layout title="Delete Project: {{ $content->title }}">
    <x-form-card title="Delete Project: {{ $content->title }}"
        description="Are you sure you want to delete this project? This action cannot be undone."
        action="{{ route('projects.destroy', $content->slug) }}" method="DELETE" class="wide">
        <x-slot name="actions">
            <button type="submit" class="btn btn--danger">
                <x-heroicon-o-trash class="btn__icon" aria-hidden="true" focusable="false" width="16"
                    height="16" />
                Yes, Delete Project
            </button>
            <a href="{{ route('projects.show', $content->slug) }}" class="btn btn-secondary">
                <x-heroicon-c-arrow-uturn-left class="btn__icon" aria-hidden="true" focusable="false" width="16"
                    height="16" />
                Cancel
            </a>
        </x-slot>
    </x-form-card>
</x-layout>
