@php
    /** @var \App\Models\Content $content */
@endphp

<x-form action="{{ route('thoughts.update', ['slug' => $content->slug]) }}" method="PUT" data-thought-edit-form>
    <div class="form-field">
        <textarea class="form-textarea" id="body-{{ $content->slug }}" name="body" required data-embed-paste-target>{{ $content->body ?? '' }}</textarea>
    </div>

    <input type="hidden" name="embeds"
        value="{{ $content->embedImages->map(fn($image) => $image->getShortHash())->implode(',') }}" />
    <div class="embed-gallery" data-embed-list></div>

    <div class="thoughts-composer-row">
        <x-visibility-radio id="visibility-{{ $content->slug }}" :selected="(string) $content->visibility->value" />
        <div class="thoughts-attach">
            <label class="btn" for="attachment-{{ $content->slug }}">
                <x-heroicon-o-paper-clip class="btn__icon" aria-hidden="true" focusable="false" width="16"
                    height="16" />
            </label>
            <input class="thoughts-attach__input" id="attachment-{{ $content->slug }}" type="file"
                accept="image/*,video/*" multiple data-embed-input />
        </div>
    </div>

    <div class="thoughts-composer-row">
        <button class="btn btn--primary" type="submit">Save</button>
        <button class="btn" type="button" data-thought-cancel>Cancel</button>
    </div>
</x-form>
