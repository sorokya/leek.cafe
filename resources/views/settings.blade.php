<x-layout title="Settings">
    <div class="settings-grid">
        <x-form-card title="User Settings" description="Update your user settings"
            action="{{ route('profile.update-settings') }}">
            <x-slot name="fields">
                @if (session('status'))
                    <div class="form-status" role="status" aria-live="polite">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="form-field">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-input" id="name" name="name" type="text" inputmode="text"
                        value="{{ old('name', $name) }}" required
                        @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                        @class(['form-input', 'form-input--invalid' => $errors->has('name')]) />

                    @error('name')
                        <p class="form-hint form-hint--error" id="name-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-field">
                    <label class="form-label" for="new-password">New Password</label>
                    <input class="form-input" id="new-password" name="new_password" type="password" inputmode="text"
                        value="{{ old('new_password') }}"
                        @error('new_password') aria-invalid="true" aria-describedby="new-password-error" @enderror
                        @class([
                            'form-input',
                            'form-input--invalid' => $errors->has('new_password'),
                        ]) />

                    @error('new_password')
                        <p class="form-hint form-hint--error" id="new-password-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="new-password-confirmation">Confirm New Password</label>
                    <input class="form-input" id="new-password-confirmation" name="new_password_confirmation"
                        type="password" inputmode="text" value="{{ old('new_password_confirmation') }}" />
                </div>

                <div class="form-field">
                    <label class="form-label" for="timezone">Time Zone</label>
                    <select class="form-input" id="timezone" name="timezone" required
                        @error('timezone') aria-invalid="true" aria-describedby="timezone-error" @enderror
                        @class([
                            'form-input',
                            'form-input--invalid' => $errors->has('timezone'),
                        ])>
                        @foreach (DateTimeZone::listIdentifiers() as $tz)
                            <option value="{{ $tz }}" @if (old('timezone', $timezone) === $tz) selected @endif>
                                {{ $tz }}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <p class="form-hint form-hint--error" id="timezone-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="password">Current Password</label>
                    <input class="form-input" id="password" name="password" type="password" inputmode="text"
                        value="{{ old('password') }}" required
                        @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        @class([
                            'form-input',
                            'form-input--invalid' => $errors->has('password'),
                        ]) />

                    @error('password')
                        <p class="form-hint form-hint--error" id="password-error">{{ $message }}</p>
                    @enderror
                </div>
            </x-slot>
            <x-slot name="actions">
                <button class="btn btn--primary" type="submit">Save Settings</button>
            </x-slot>
        </x-form-card>

        <x-card title="Media Statuses" description="Manage media statuses." aria-label="Media statuses">
            <div class="form">
                <div class="form-field">
                    <p class="form-label">Media status</p>

                    <form method="post" action="{{ route('media-statuses.store') }}">
                        @csrf
                        <div class="form-actions">
                            <input class="form-input" name="status" type="text" inputmode="text"
                                value="{{ old('status') }}" placeholder="Status" required style="flex: 1" />
                            <input class="form-input" name="icon" type="text" inputmode="text"
                                value="{{ old('icon') }}" placeholder="Icon" style="flex: 1" />
                            <input name="color" type="hidden" value="{{ old('color') }}" />
                            <input class="form-color" type="color" value="{{ old('color') ?: '#FFFFFF' }}"
                                aria-label="Color" title="Color"
                                oninput="this.previousElementSibling.value = this.value" />
                            <button class="btn btn--primary" type="submit">Add</button>
                        </div>
                        @error('status')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('icon')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('color')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                    </form>

                    @if (($mediaStatuses ?? collect())->count() > 0)
                        <div class="form-field">
                            <p class="form-hint">Existing statuses</p>

                            @foreach ($mediaStatuses as $mediaStatus)
                                <div class="form-actions">
                                    <form method="post" action="{{ route('media-statuses.update', $mediaStatus) }}"
                                        style="display: flex; gap: 0.75rem; align-items: center; flex: 1">
                                        @csrf
                                        @method('PUT')

                                        <input class="form-input" name="status_value" type="text"
                                            placeholder="Status" inputmode="text" value="{{ $mediaStatus->status }}"
                                            required style="flex: 1" />
                                        <input class="form-input" name="icon_value" type="text" inputmode="text"
                                            placeholder="Icon" value="{{ $mediaStatus->icon }}" style="flex: 1" />
                                        <input name="color_value" type="hidden"
                                            value="{{ $mediaStatus->color }}" />
                                        <input class="form-color" type="color"
                                            value="{{ $mediaStatus->color ?: '#FFFFFF' }}" aria-label="Color"
                                            title="Color" oninput="this.previousElementSibling.value = this.value" />
                                        <button class="btn" type="submit">Update</button>
                                    </form>

                                    <form method="post"
                                        action="{{ route('media-statuses.destroy', $mediaStatus) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn--danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            @endforeach

                            @error('status_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('icon_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('color_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

            </div>
        </x-card>

        <x-card title="Media Types" description="Manage media types." aria-label="Media types">
            <div class="form">
                <div class="form-field">
                    <p class="form-label">Media type</p>

                    <form method="post" action="{{ route('media-types.store') }}">
                        @csrf
                        <div class="form-actions">
                            <input class="form-input" name="type" type="text" inputmode="text"
                                value="{{ old('type') }}" placeholder="Add a new type" required style="flex: 1" />
                            <button class="btn btn--primary" type="submit">Add</button>
                        </div>
                        @error('type')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                    </form>

                    @if (($mediaTypes ?? collect())->count() > 0)
                        <div class="form-field">
                            <p class="form-hint">Existing types</p>

                            @foreach ($mediaTypes as $mediaType)
                                <div class="form-actions">
                                    <form method="post" action="{{ route('media-types.update', $mediaType) }}"
                                        style="display: flex; gap: 0.75rem; align-items: center; flex: 1">
                                        @csrf
                                        @method('PUT')

                                        <input class="form-input" name="type_value" type="text" inputmode="text"
                                            value="{{ old('type_value', $mediaType->type) }}" required
                                            style="flex: 1" />
                                        <button class="btn" type="submit">Update</button>
                                    </form>

                                    <form method="post" action="{{ route('media-types.destroy', $mediaType) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn--danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            @endforeach

                            @error('type_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </x-card>
    </div>
</x-layout>
