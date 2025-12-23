<x-layout>
    <x-form action="{{ route('auth.store-set-password') }}" title="Set Password"
        description="Create a password for your account.">
        <x-slot name="fields">
            <input type="hidden" name="username" value="{{ $username ?? request('username') }}" />

            <div class="form-field">
                <label class="form-label" for="password">New Password</label>
                <input class="form-input" id="password" name="password" type="password" autocomplete="new-password"
                    required />
            </div>
            <div class="form-field">
                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                <input class="form-input" id="password_confirmation" name="password_confirmation" type="password"
                    autocomplete="new-password" required />
            </div>
        </x-slot>
        <x-slot name="actions">
            <button class="btn btn--primary" type="submit">Set Password</button>
        </x-slot>
    </x-form>
</x-layout>
