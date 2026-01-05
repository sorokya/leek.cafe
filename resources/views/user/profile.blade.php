<x-layout title="{{ $profileUser->name }} ({{ '@' . $profileUser->username }})">
    <div class="stack">
        <header class="section" aria-label="Profile header">
            <div class="section__content">
                <h1 class="section__title" style="padding: 0">
                    {{ $profileUser->name }} <a
                        href="{{ route('user.profile', ['user' => $profileUser->username]) }}">{{ '@' . $profileUser->username }}</a>
                </h1>
            </div>
        </header>

        <div class="profile-grid">
            <div class="stack">
                <section class="section" aria-label="Statistics">
                    <header class="section__header">
                        <h2 class="section__title">
                            Statistics
                        </h2>
                    </header>
                    <div class="section__content">
                        <p class="content-meta">Coming soon.</p>
                    </div>
                </section>

                <section class="section" aria-label="Habits">
                    <header class="section__header">
                        <h2 class="section__title">
                            Habits
                        </h2>
                    </header>
                    <div class="section__content">
                        <p class="content-meta">Coming soon.</p>
                    </div>
                </section>

                <div data-day-view>
                    @include('user._day')
                </div>
            </div>

            <section class="section" aria-label="Activity feed">
                <header class="section__header">
                    <h2 class="section__title">
                        Activity
                    </h2>
                </header>
                <div class="section__content">
                    @if ($activityFeed->isEmpty())
                        <p class="content-meta">No recent activity.</p>
                    @else
                        <div class="content-feed">
                            @foreach ($activityFeed as $content)
                                @if ($content->content_type === \App\ContentType::POST)
                                    @include('partials.post-summary', [
                                        'content' => $content,
                                        'link' => route('posts.show', ['slug' => $content->slug]),
                                        'showDate' => true,
                                    ])
                                @elseif ($content->content_type === \App\ContentType::PROJECT)
                                    @include('partials.project-summary', [
                                        'content' => $content,
                                        'link' => route('projects.show', ['slug' => $content->slug]),
                                    ])
                                @elseif ($content->content_type === \App\ContentType::THOUGHT)
                                    @include('partials.thought-feed-item', [
                                        'content' => $content,
                                    ])
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/user-profile.js')
    @endpush
</x-layout>
