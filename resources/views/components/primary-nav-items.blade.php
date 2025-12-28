@props([
    'mobile' => false,
])

<a class="nav-link {{ request()->is('/') ? 'is-active' : '' }}" href="/" aria-label="Home" title="Home">
    <x-heroicon-o-home class="nav-link__icon" aria-hidden="true" focusable="false" width="18" height="18" />
    {{ $mobile || request()->is('/') ? 'Home' : '' }}
</a>
<a class="nav-link {{ request()->is('projects*') ? 'is-active' : '' }}" href="/projects" aria-label="Projects"
    title="Projects">
    <x-heroicon-o-code-bracket class="nav-link__icon" aria-hidden="true" focusable="false" width="18"
        height="18" />
    {{ $mobile || request()->is('projects*') ? 'Projects' : '' }}
</a>
<a class="nav-link {{ request()->is('posts*') ? 'is-active' : '' }}" href="/posts" aria-label="Posts" title="Posts">
    <x-heroicon-o-newspaper class="nav-link__icon" aria-hidden="true" focusable="false" width="18" height="18" />
    {{ $mobile || request()->is('posts*') ? 'Posts' : '' }}
</a>
<a class="nav-link {{ request()->is('media*') ? 'is-active' : '' }}" href="/media" aria-label="Media" title="Media">
    <x-heroicon-o-film class="nav-link__icon" aria-hidden="true" focusable="false" width="18" height="18" />
    {{ $mobile || request()->is('media*') ? 'Media' : '' }}
</a>
<a class="nav-link" {{ request()->is('thoughts') ? 'is-active' : '' }} href="/thoughts" aria-label="Thoughts"
    title="Thoughts">
    <x-heroicon-o-light-bulb class="nav-link__icon" aria-hidden="true" focusable="false" width="18"
        height="18" />
    {{ $mobile || request()->is('thoughts') ? 'Thoughts' : '' }}
</a>

@auth
    <a class="nav-link {{ request()->is('settings*') ? 'is-active' : '' }}" href="/settings" aria-label="Settings"
        title="Settings">
        <x-heroicon-o-cog-6-tooth class="nav-link__icon" aria-hidden="true" focusable="false" width="18"
            height="18" />
        {{ $mobile || request()->is('settings*') ? 'Settings' : '' }}
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
    <a class="nav-link {{ request()->is('login') ? 'is-active' : '' }}" href="{{ route('auth.show-login') }}"
        aria-label="Login" title="Login">
        <x-heroicon-o-arrow-right-on-rectangle class="nav-link__icon" aria-hidden="true" focusable="false" width="18"
            height="18" />
        {{ $mobile || request()->is('login') ? 'Login' : '' }}
    </a>
@endauth
