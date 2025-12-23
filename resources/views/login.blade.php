<x-layout title="Login">
    <x-form action="{{ route('auth.store-login') }}" title="Login"
        description="Use your username and password to continue.">
        <x-slot name="fields">
            <div class="form-field">
                <label class="form-label" for="username">Username</label>
                <input class="form-input" id="username" name="username" type="text" inputmode="text"
                    autocomplete="username" value="{{ old('username') }}" required
                    @error('username') aria-invalid="true" aria-describedby="username-error" @enderror
                    @class([
                        'form-input',
                        'form-input--invalid' => $errors->has('username'),
                    ]) />

                @error('username')
                    <p class="form-hint form-hint--error" id="username-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" id="password" name="password" type="password" autocomplete="current-password"
                    required @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
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
            <div>
                <label class="form-checkbox" for="remember">
                    <input class="form-checkbox__input" id="remember" name="remember" type="checkbox" value="1"
                        @checked(old('remember')) />
                    <span>Remember me</span>
                </label>

                @error('remember')
                    <p class="form-hint form-hint--error" id="remember-error">{{ $message }}</p>
                @enderror
            </div>
            <button class="btn btn--primary" type="submit">Login</button>
        </x-slot>
    </x-form>
</x-layout>
