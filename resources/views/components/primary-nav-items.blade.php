<a class="nav-link {{ request()->is('/') ? 'is-active' : '' }}" href="/">Home</a>
<a class="nav-link {{ request()->is('projects*') ? 'is-active' : '' }}" href="/projects">Projects</a>
<a class="nav-link {{ request()->is('posts*') ? 'is-active' : '' }}" href="/posts">Posts</a>

@auth
    <a class="nav-link" href="/settings">Settings</a>

    <form method="POST" action="{{ route('auth.logout') }}">
        @csrf
        <button class="nav-link" type="submit">Logout</button>
    </form>
@endauth
