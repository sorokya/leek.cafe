@props([
    'title' => null,
    'description' => null,
    'action' => '#',
    'method' => 'POST',
    'encType' => 'application/x-www-form-urlencoded',
    'class' => '',
    'fields' => null,
    'actions' => null,
])

<x-card :title="$title" :description="$description" :class="$class" :aria-label="$title ?? 'Form'">
    <x-form :action="$action" :method="$method" :enc-type="$encType">
        {{ $fields }}

        @if ($actions)
            <div class="form-actions">
                {{ $actions }}
            </div>
        @endif
    </x-form>
</x-card>
