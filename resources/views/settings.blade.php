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

        <x-card title="Media Statuses" description="Manage media statuses." aria-label="Media statuses"
            class="wide">
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
                                    <x-status-pill :icon="$mediaStatus->icon" :status="$mediaStatus->status" :bg="$mediaStatus->color
                                        ? 'color-mix(in oklab, ' . $mediaStatus->color . ' 22%, var(--bg))'
                                        : 'var(--surface)'"
                                        :fg="$mediaStatus->color
                                            ? 'color-mix(in oklab, ' . $mediaStatus->color . ' 30%, var(--text))'
                                            : 'var(--text)'" />

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

        <x-card title="Metrics" description="Manage daily metrics." aria-label="Metrics" class="wide">
            <div class="form">
                <div class="form-field">
                    <p class="form-label">Metric</p>

                    <form method="post" action="{{ route('metrics.store') }}">
                        @csrf
                        <div class="form-actions" style="flex-wrap: wrap">
                            <input class="form-input" name="metric_name" type="text" inputmode="text"
                                value="{{ old('metric_name') }}" placeholder="Name" required style="flex: 1" />

                            <select class="form-input" name="metric_visibility" required style="flex: 1">
                                <option value="{{ \App\Visibility::PRIVATE->value }}"
                                    @if (
                                        (string) old('metric_visibility', (string) \App\Visibility::PRIVATE->value) ===
                                            (string) \App\Visibility::PRIVATE->value) selected @endif>
                                    Private
                                </option>
                                <option value="{{ \App\Visibility::PUBLIC->value }}"
                                    @if ((string) old('metric_visibility') === (string) \App\Visibility::PUBLIC->value) selected @endif>
                                    Public
                                </option>
                            </select>

                            <input class="form-input" name="metric_icon" type="text" inputmode="text"
                                value="{{ old('metric_icon') }}" placeholder="Icon (heroicon-*)" style="flex: 1" />
                            <input name="metric_color" type="hidden" value="{{ old('metric_color') }}" />
                            <input class="form-color" type="color" value="{{ old('metric_color') ?: '#FFFFFF' }}"
                                aria-label="Color" title="Color"
                                oninput="this.previousElementSibling.value = this.value" />
                        </div>

                        <div class="form-actions" style="flex-wrap: wrap; margin-top: 0.75rem">
                            <input class="form-input" name="metric_min" type="number" inputmode="decimal"
                                step="0.01" value="{{ old('metric_min') }}" placeholder="Min (optional)"
                                style="flex: 1" />
                            <input class="form-input" name="metric_max" type="number" inputmode="decimal"
                                step="0.01" value="{{ old('metric_max') }}" placeholder="Max (optional)"
                                style="flex: 1" />
                            <input class="form-input" name="metric_options" type="text" inputmode="text"
                                value="{{ old('metric_options') }}" placeholder="Options (CSV, optional)"
                                style="flex: 2" />
                            <button class="btn btn--primary" type="submit">Add</button>
                        </div>

                        @error('metric_name')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('metric_visibility')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('metric_icon')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('metric_color')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('metric_min')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('metric_max')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('metric_options')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                    </form>

                    @if (($metrics ?? collect())->count() > 0)
                        <div class="form-field">
                            <p class="form-hint">Existing metrics</p>

                            @foreach ($metrics as $metric)
                                <div class="form-actions" style="flex-wrap: wrap">
                                    <x-status-pill :icon="$metric->icon" :status="$metric->name" :bg="$metric->color
                                        ? 'color-mix(in oklab, ' . $metric->color . ' 22%, var(--bg))'
                                        : 'var(--surface)'"
                                        :fg="$metric->color
                                            ? 'color-mix(in oklab, ' . $metric->color . ' 30%, var(--text))'
                                            : 'var(--text)'" />

                                    <form method="post" action="{{ route('metrics.update', $metric) }}"
                                        style="display: flex; gap: 0.75rem; align-items: center; flex: 1; flex-wrap: wrap">
                                        @csrf
                                        @method('PUT')

                                        <input class="form-input" name="metric_name_value" type="text"
                                            inputmode="text" value="{{ old('metric_name_value', $metric->name) }}"
                                            required style="flex: 2" />

                                        <select class="form-input" name="metric_visibility_value" required
                                            style="flex: 1">
                                            <option value="{{ \App\Visibility::PRIVATE->value }}"
                                                @if (
                                                    (string) old('metric_visibility_value', (string) $metric->visibility->value) ===
                                                        (string) \App\Visibility::PRIVATE->value) selected @endif>
                                                Private
                                            </option>
                                            <option value="{{ \App\Visibility::PUBLIC->value }}"
                                                @if (
                                                    (string) old('metric_visibility_value', (string) $metric->visibility->value) ===
                                                        (string) \App\Visibility::PUBLIC->value) selected @endif>
                                                Public
                                            </option>
                                        </select>

                                        <input class="form-input" name="metric_icon_value" type="text"
                                            inputmode="text" value="{{ old('metric_icon_value', $metric->icon) }}"
                                            placeholder="Icon" style="flex: 1" />
                                        <input name="metric_color_value" type="hidden"
                                            value="{{ $metric->color }}" />
                                        <input class="form-color" type="color"
                                            value="{{ $metric->color ?: '#FFFFFF' }}" aria-label="Color"
                                            title="Color" oninput="this.previousElementSibling.value = this.value" />

                                        <input class="form-input" name="metric_min_value" type="number"
                                            inputmode="decimal" step="0.01"
                                            value="{{ old('metric_min_value', $metric->min) }}" placeholder="Min"
                                            style="flex: 1" />
                                        <input class="form-input" name="metric_max_value" type="number"
                                            inputmode="decimal" step="0.01"
                                            value="{{ old('metric_max_value', $metric->max) }}" placeholder="Max"
                                            style="flex: 1" />
                                        <input class="form-input" name="metric_options_value" type="text"
                                            inputmode="text"
                                            value="{{ old('metric_options_value', $metric->options) }}"
                                            placeholder="Options (CSV)" style="flex: 2" />

                                        <button class="btn" type="submit">Update</button>
                                    </form>

                                    <form method="post" action="{{ route('metrics.destroy', $metric) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn--danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            @endforeach

                            @error('metric_name_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('metric_visibility_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('metric_icon_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('metric_color_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('metric_min_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('metric_max_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('metric_options_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        <x-card title="Habits" description="Manage daily habits." aria-label="Habits" class="wide">
            <div class="form">
                <div class="form-field">
                    <p class="form-label">Habit</p>

                    <form method="post" action="{{ route('habits.store') }}">
                        @csrf
                        <div class="form-actions" style="flex-wrap: wrap">
                            <input class="form-input" name="habit_name" type="text" inputmode="text"
                                value="{{ old('habit_name') }}" placeholder="Name" required style="flex: 1" />

                            <select class="form-input" name="habit_visibility" required style="flex: 1">
                                <option value="{{ \App\Visibility::PRIVATE->value }}"
                                    @if (
                                        (string) old('habit_visibility', (string) \App\Visibility::PRIVATE->value) ===
                                            (string) \App\Visibility::PRIVATE->value) selected @endif>
                                    Private
                                </option>
                                <option value="{{ \App\Visibility::PUBLIC->value }}"
                                    @if ((string) old('habit_visibility') === (string) \App\Visibility::PUBLIC->value) selected @endif>
                                    Public
                                </option>
                            </select>

                            <input class="form-input" name="habit_icon" type="text" inputmode="text"
                                value="{{ old('habit_icon') }}" placeholder="Icon (heroicon-*)" style="flex: 1" />
                            <input name="habit_color" type="hidden" value="{{ old('habit_color') }}" />
                            <input class="form-color" type="color" value="{{ old('habit_color') ?: '#FFFFFF' }}"
                                aria-label="Color" title="Color"
                                oninput="this.previousElementSibling.value = this.value" />
                            <button class="btn btn--primary" type="submit">Add</button>
                        </div>

                        @error('habit_name')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('habit_visibility')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('habit_icon')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                        @error('habit_color')
                            <p class="form-hint form-hint--error">{{ $message }}</p>
                        @enderror
                    </form>

                    @if (($habits ?? collect())->count() > 0)
                        <div class="form-field">
                            <p class="form-hint">Existing habits</p>

                            @foreach ($habits as $habit)
                                <div class="form-actions" style="flex-wrap: wrap">
                                    <x-status-pill :icon="$habit->icon" :status="$habit->name" :bg="$habit->color
                                        ? 'color-mix(in oklab, ' . $habit->color . ' 22%, var(--bg))'
                                        : 'var(--surface)'"
                                        :fg="$habit->color
                                            ? 'color-mix(in oklab, ' . $habit->color . ' 30%, var(--text))'
                                            : 'var(--text)'" />

                                    <form method="post" action="{{ route('habits.update', $habit) }}"
                                        style="display: flex; gap: 0.75rem; align-items: center; flex: 1; flex-wrap: wrap">
                                        @csrf
                                        @method('PUT')

                                        <input class="form-input" name="habit_name_value" type="text"
                                            inputmode="text" value="{{ old('habit_name_value', $habit->name) }}"
                                            required style="flex: 2" />

                                        <select class="form-input" name="habit_visibility_value" required
                                            style="flex: 1">
                                            <option value="{{ \App\Visibility::PRIVATE->value }}"
                                                @if (
                                                    (string) old('habit_visibility_value', (string) $habit->visibility->value) ===
                                                        (string) \App\Visibility::PRIVATE->value) selected @endif>
                                                Private
                                            </option>
                                            <option value="{{ \App\Visibility::PUBLIC->value }}"
                                                @if (
                                                    (string) old('habit_visibility_value', (string) $habit->visibility->value) ===
                                                        (string) \App\Visibility::PUBLIC->value) selected @endif>
                                                Public
                                            </option>
                                        </select>

                                        <input class="form-input" name="habit_icon_value" type="text"
                                            inputmode="text" value="{{ old('habit_icon_value', $habit->icon) }}"
                                            placeholder="Icon" style="flex: 1" />
                                        <input name="habit_color_value" type="hidden"
                                            value="{{ $habit->color }}" />
                                        <input class="form-color" type="color"
                                            value="{{ $habit->color ?: '#FFFFFF' }}" aria-label="Color"
                                            title="Color" oninput="this.previousElementSibling.value = this.value" />

                                        <button class="btn" type="submit">Update</button>
                                    </form>

                                    <form method="post" action="{{ route('habits.destroy', $habit) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn--danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            @endforeach

                            @error('habit_name_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('habit_visibility_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('habit_icon_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                            @error('habit_color_value')
                                <p class="form-hint form-hint--error">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>
        </x-card>
    </div>
</x-layout>
