<a class="nav-link {{ request()->is('/') ? 'is-active' : '' }}" href="/">
    <x-heroicon-o-home class="nav-link__icon" aria-hidden="true" focusable="false" width="18" height="18" />
    Home
</a>
<a class="nav-link {{ request()->is('projects*') ? 'is-active' : '' }}" href="/projects">
    <x-heroicon-o-code-bracket class="nav-link__icon" aria-hidden="true" focusable="false" width="18"
        height="18" />
    Projects
</a>
<a class="nav-link {{ request()->is('posts*') ? 'is-active' : '' }}" href="/posts">
    <x-heroicon-o-document-text class="nav-link__icon" aria-hidden="true" focusable="false" width="18"
        height="18" />
    Posts
</a>
<a class="nav-link {{ request()->is('media*') ? 'is-active' : '' }}" href="/media">
    <x-heroicon-o-film class="nav-link__icon" aria-hidden="true" focusable="false" width="18" height="18" />
    Media
</a>

@auth
    <a class="nav-link {{ request()->is('settings*') ? 'is-active' : '' }}" href="/settings">
        <x-heroicon-o-cog-6-tooth class="nav-link__icon" aria-hidden="true" focusable="false" width="18"
            height="18" />
        Settings
    </a>

    <form method="POST" action="{{ route('auth.logout') }}">
        @csrf
        <button class="nav-link" type="submit">
            <x-heroicon-o-arrow-right-on-rectangle class="nav-link__icon" aria-hidden="true" focusable="false"
                width="18" height="18" />
            Logout
        </button>
    </form>
@else
    <a class="nav-link {{ request()->is('login') ? 'is-active' : '' }}" href="{{ route('auth.show-login') }}">
        <x-heroicon-o-arrow-right-on-rectangle class="nav-link__icon" aria-hidden="true" focusable="false" width="18"
            height="18" />
        Login
    </a>
@endauth
