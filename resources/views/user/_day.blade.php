<section class="section" aria-label="Day view">
    <header class="section__header">
        <h2 class="section__title" style="align-items: center; justify-content: space-between">
            <a class="content-link" href="{{ route('user.profile.date', [$profileUser, $day->format('Y-m-d')]) }}"
                data-day-link>
                {{ $day->format('l, F jS Y') }}
            </a>

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
            <div>
                <h3 class="content-title" style="margin-bottom: 0.5rem">Metrics</h3>

                @if ($metrics->isEmpty())
                    <p class="content-meta">No metrics configured.</p>
                @else
                    @if ($isOwner)
                        <x-form action="{{ route('user.day.metrics.store', [$profileUser, $day->format('Y-m-d')]) }}"
                            method="POST">
                            <div class="form">
                                @foreach ($metrics as $metric)
                                    @php($entry = $metricEntries->get($metric->id))
                                    <div class="form-field">
                                        <label class="form-label">{{ $metric->name }}</label>

                                        @if ($metric->hasOptions())
                                            <input type="hidden" name="metrics[{{ $metric->id }}]" value="" />
                                            <fieldset class="toggle-group" aria-label="{{ $metric->name }}">
                                                <div class="toggle-group__inner" role="radiogroup"
                                                    aria-label="{{ $metric->name }}">
                                                    @foreach ($metric->optionList() as $option)
                                                        @php($optionId = 'metric-' . $metric->id . '-option-' . $loop->index)
                                                        <input class="toggle-group__input" type="radio"
                                                            name="metrics[{{ $metric->id }}]"
                                                            id="{{ $optionId }}" value="{{ $option }}"
                                                            @checked((string) old('metrics.' . $metric->id, $entry?->value) === (string) $option) />
                                                        <label class="toggle-group__button" for="{{ $optionId }}">
                                                            {{ $option }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </fieldset>
                                        @else
                                            <input class="form-input" type="number" inputmode="decimal" step="0.01"
                                                name="metrics[{{ $metric->id }}]"
                                                value="{{ old('metrics.' . $metric->id, $entry?->value) }}"
                                                @if ($metric->min !== null) min="{{ $metric->min }}" @endif
                                                @if ($metric->max !== null) max="{{ $metric->max }}" @endif />
                                        @endif

                                        @error('metrics.' . $metric->id)
                                            <p class="form-hint form-hint--error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach

                                <div class="form-actions">
                                    <button class="btn btn--primary" type="submit">Save Metrics</button>
                                </div>
                            </div>
                        </x-form>
                    @else
                        <div class="form">
                            @foreach ($metrics as $metric)
                                @php($entry = $metricEntries->get($metric->id))
                                <div class="form-field">
                                    <div class="form-label">{{ $metric->name }}</div>
                                    <div class="content-meta">{{ $entry?->value ?? '—' }}</div>
                                </div>
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
                        <x-form action="{{ route('user.day.habits.store', [$profileUser, $day->format('Y-m-d')]) }}"
                            method="POST">
                            <div class="form">
                                @foreach ($habits as $habit)
                                    @php($entry = $habitEntries->get($habit->id))
                                    <div class="form-field">
                                        <label class="form-checkbox">
                                            <input class="form-checkbox__input" type="checkbox"
                                                name="habits[{{ $habit->id }}]" value="1"
                                                @checked((bool) old('habits.' . $habit->id, $entry?->done ?? false)) />
                                            <span>{{ $habit->name }}</span>
                                        </label>
                                    </div>
                                @endforeach

                                <div class="form-actions">
                                    <button class="btn btn--primary" type="submit">Save Habits</button>
                                </div>
                            </div>
                        </x-form>
                    @else
                        <div class="form">
                            @foreach ($habits as $habit)
                                @php($entry = $habitEntries->get($habit->id))
                                <div class="form-field">
                                    <div class="form-label">{{ $habit->name }}</div>
                                    <div class="content-meta">{{ $entry?->done ?? false ? 'Yes' : 'No' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

            <div>
                <h3 class="content-title" style="margin-bottom: 0.5rem">Content this day</h3>

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
