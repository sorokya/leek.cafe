<x-layout title="Settings">
    <x-form title="User Settings" description="Update your user settings" action="{{ route('profile.update-settings') }}">
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
    </x-form>
</x-layout>
