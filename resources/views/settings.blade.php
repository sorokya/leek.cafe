<x-layout title="Settings">
    <x-form title="User Settings" description="Update your user settings" action="#">
        <x-slot name="fields">
            <div class="form-field">
                <label class="form-label" for="name">Name</label>
                <input class="form-input" id="name" name="name" type="text" inputmode="text"
                    value="{{ old('name', $name) }}" required />
            </div>
            <div class="form-field">
                <label class="form-label" for="new-password">New Password</label>
                <input class="form-input" id="new-password" name="new_password" type="password" inputmode="text"
                    value="{{ old('new_password') }}" />
            </div>

            <div class="form-field">
                <label class="form-label" for="new-password-confirmation">Confirm New Password</label>
                <input class="form-input" id="new-password-confirmation" name="new_password_confirmation"
                    type="password" inputmode="text" value="{{ old('new_password_confirmation') }}" />
            </div>

            <div class="form-field">
                <label class="form-label" for="password">Current Password</label>
                <input class="form-input" id="password" name="password" type="password" inputmode="text"
                    value="{{ old('password') }}" required />
            </div>
        </x-slot>
        <x-slot name="actions">
            <button class="btn btn--primary" type="submit">Save Settings</button>
        </x-slot>
    </x-form>
</x-layout>
