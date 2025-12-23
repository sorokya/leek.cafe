<x-layout title="Login">
    <x-form action="{{ route('auth.store-login') }}" title="Login"
        description="Use your username and password to continue.">
        <x-slot name="fields">
            <div class="form-field">
                <label class="form-label" for="username">Username</label>
                <input class="form-input" id="username" name="username" type="text" inputmode="text"
                    autocomplete="username" required />
            </div>

            <div class="form-field">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" id="password" name="password" type="password" autocomplete="current-password"
                    required />
            </div>
        </x-slot>
        <x-slot name="actions">
            <label class="form-checkbox" for="remember">
                <input class="form-checkbox__input" id="remember" name="remember" type="checkbox" />
                <span>Remember me</span>
            </label>
            <button class="btn btn--primary" type="submit">Login</button>
        </x-slot>
    </x-form>
</x-layout>
