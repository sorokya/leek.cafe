<section class="section" aria-label="Day view">
    <header class="section__header">
        <h2 class="section__title" style="align-items: center; justify-content: space-between">
            <div style="display: flex; align-items: center; gap: 0.5rem">
                <x-heroicon-o-calendar-days class="section__title-icon" aria-hidden="true" focusable="false" width="24"
                    height="24" /> <a class="content-link"
                    href="{{ route('user.profile.date', [$profileUser, $day->format('Y-m-d')]) }}" data-day-link>

                    {{ $day->format('l, F jS Y') }}
                </a>
            </div>

            <span class="section__actions" style="margin: 0; justify-content: flex-end">
                @php($prev = $day->subDay()->format('Y-m-d'))
                @php($next = $day->addDay()->format('Y-m-d'))

                <a class="btn btn--small" href="{{ route('user.profile.date', [$profileUser, $prev]) }}" data-day-prev
                    aria-label="Previous day">
                    <x-heroicon-o-arrow-left aria-hidden="true" focusable="false" width="16" height="16" />
                </a>

                <button class="btn btn--small" type="button" data-day-calendar aria-label="Pick a date">
                    <x-heroicon-o-calendar-days aria-hidden="true" focusable="false" width="16" height="16" />
                </button>
                <input class="form-input" type="date" value="{{ $day->format('Y-m-d') }}" data-day-picker
                    style="max-width: 12rem" />

                <a class="btn btn--small" href="{{ route('user.profile.date', [$profileUser, $next]) }}" data-day-next
                    aria-label="Next day">
                    <x-heroicon-o-arrow-right aria-hidden="true" focusable="false" width="16" height="16" />
                </a>
            </span>
        </h2>
    </header>

    <div class="section__content">
        <div class="stack" style="gap: 1rem">
            @if ($isOwner)
                <x-form action="{{ route('user.day.store', [$profileUser, $day->format('Y-m-d')]) }}" method="POST"
                    data-day-save-form>
            @endif
            <div>

                <h3 class="content-title" style="margin-bottom: 0.5rem">Metrics</h3>

                @if ($metrics->isEmpty())
                    <p class="content-meta">No metrics configured.</p>
                @else
                    @if ($isOwner)
                        <div class="metrics">
                            @foreach ($metrics as $metric)
                                @php($entry = $metricEntries->get($metric->id))
                                @php($rawValue = old('metrics.' . $metric->id, $entry?->value))
                                @php($value = \App\Support\MetricValueFormatter::format($rawValue))

                                <div class="day-metric-edit">
                                    <x-status-pill :icon="$metric->icon" :status="$metric->name" :bg="$metric->color
                                        ? 'color-mix(in oklab, ' . $metric->color . ' 22%, var(--bg))'
                                        : 'var(--surface)'"
                                        :fg="$metric->color
                                            ? 'color-mix(in oklab, ' . $metric->color . ' 30%, var(--text))'
                                            : 'var(--text)'" />

                                    @if ($metric->hasOptions())
                                        <input type="hidden" name="metrics[{{ $metric->id }}]" value="" />
                                        <fieldset class="toggle-group" aria-label="{{ $metric->name }}">
                                            <div class="toggle-group__inner" role="radiogroup"
                                                aria-label="{{ $metric->name }}">
                                                @foreach ($metric->optionList() as $option)
                                                    @php($optionId = 'metric-' . $metric->id . '-option-' . $loop->index)
                                                    <input class="toggle-group__input" type="radio"
                                                        name="metrics[{{ $metric->id }}]" id="{{ $optionId }}"
                                                        value="{{ $option }}" @checked((string) (\App\Support\MetricValueFormatter::format($rawValue) ?? '') === (string) $option) />
                                                    <label class="toggle-group__button" for="{{ $optionId }}">
                                                        {{ $option }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </fieldset>
                                    @else
                                        <input class="form-input day-metric-edit__number" type="number"
                                            inputmode="numeric" step="1" name="metrics[{{ $metric->id }}]"
                                            value="{{ $value }}"
                                            @if ($metric->min !== null) min="{{ $metric->min }}" @endif
                                            @if ($metric->max !== null) max="{{ $metric->max }}" @endif />
                                    @endif

                                    @error('metrics.' . $metric->id)
                                        <p class="form-hint form-hint--error">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="metrics">
                            @foreach ($metrics as $metric)
                                @php($entry = $metricEntries->get($metric->id))
                                <x-status-pill :icon="$metric->icon"
                                    status="{{ $metric->name . ': ' . ($entry ? \App\Support\MetricValueFormatter::format($entry->value) ?? '—' : '—') }}"
                                    :bg="$metric->color
                                        ? 'color-mix(in oklab, ' . $metric->color . ' 22%, var(--bg))'
                                        : 'var(--surface)'" :fg="$metric->color
                                        ? 'color-mix(in oklab, ' . $metric->color . ' 30%, var(--text))'
                                        : 'var(--text)'" />
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            <div>
                <h3 class="content-title" style="margin-bottom: 0.5rem">Habits</h3>

                @if ($habits->isEmpty())
                    <p class="content-meta">No habits configured.</p>
                @else
                    @if ($isOwner)
                        <div class="habits">
                            @foreach ($habits as $habit)
                                @php($entry = $habitEntries->get($habit->id))
                                @php($done = (bool) old('habits.' . $habit->id, $entry?->done ?? false))

                                <label class="day-habit-pill">
                                    <input class="toggle-group__input" type="checkbox"
                                        name="habits[{{ $habit->id }}]" value="1"
                                        @checked($done) />
                                    <x-status-pill :icon="$habit->icon" :bg="$habit->color
                                        ? 'color-mix(in oklab, ' . $habit->color . ' 22%, var(--bg))'
                                        : 'var(--surface)'" :fg="$habit->color
                                        ? 'color-mix(in oklab, ' . $habit->color . ' 30%, var(--text))'
                                        : 'var(--text)'"
                                        style="--status-pill-bg-checked: {{ $habit->color ? 'color-mix(in oklab, ' . $habit->color . ' 32%, var(--bg))' : 'var(--accent-soft)' }}; --status-pill-fg-checked: var(--text);">
                                        <span>{{ $habit->name }}: <span class="habit-pill__state"
                                                aria-hidden="true"></span></span>
                                    </x-status-pill>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="habits">
                            @foreach ($habits as $habit)
                                @php($entry = $habitEntries->get($habit->id))
                                <x-status-pill :icon="$habit->icon"
                                    status="{{ $habit->name . ': ' . ($entry ? '✓' : '✗') }}" :bg="$habit->color
                                        ? 'color-mix(in oklab, ' . $habit->color . ' 22%, var(--bg))'
                                        : 'var(--surface)'"
                                    :fg="$habit->color
                                        ? 'color-mix(in oklab, ' . $habit->color . ' 30%, var(--text))'
                                        : 'var(--text)'" />
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            @if ($isOwner)
                <div class="form-actions">
                    <button class="nav-link" type="submit">Save</button>
                </div>

                </x-form>
            @endif

            <div>
                <h3 class="content-title" style="margin-bottom: 0.5rem">Content</h3>

                @if ($contentForDay->isEmpty())
                    <p class="content-meta">No content created on this day.</p>
                @else
                    <div class="content-feed">
                        @foreach ($contentForDay as $content)
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
        </div>
    </div>
</section>
