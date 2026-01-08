<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\ContentType;
use App\Models\Content;
use App\Models\HabitEntry;
use App\Models\MetricEntry;
use App\Models\User;
use App\Services\ContentExcerptGenerator;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class UserProfileController extends Controller
{
    public function home(ContentExcerptGenerator $excerptGenerator): View
    {
        $user = User::query()
            ->where('primary', true)->oldest()
            ->first();

        abort_if(! $user instanceof User, 500, 'No primary user defined.');

        return $this->show($user, $excerptGenerator);
    }

    public function show(User $user, ContentExcerptGenerator $excerptGenerator): View
    {
        $date = CarbonImmutable::now($user->timezone)->startOfDay();

        return $this->showDate($user, $date->format('Y-m-d'), $excerptGenerator);
    }

    public function showDate(User $user, string $date, ContentExcerptGenerator $excerptGenerator): View
    {
        $day = CarbonImmutable::createFromFormat('Y-m-d', $date, $user->timezone);
        abort_unless($day instanceof CarbonImmutable, 404);

        [$startUtc, $endUtc] = $this->dayRangeUtc($day);

        $isOwner = Auth::id() === $user->id;

        $metrics = $user->metrics()
            ->visibleTo(Auth::user())
            ->orderBy('name')
            ->get();

        $habits = $user->habits()
            ->visibleTo(Auth::user())
            ->orderBy('name')
            ->get();

        $metricEntries = MetricEntry::query()
            ->where('user_id', $user->id)
            ->where('date', $day->format('Y-m-d'))
            ->whereIn('metric_id', $metrics->pluck('id'))
            ->get()
            ->keyBy('metric_id');

        $habitEntries = HabitEntry::query()
            ->where('user_id', $user->id)
            ->where('date', $day->format('Y-m-d'))
            ->whereIn('habit_id', $habits->pluck('id'))
            ->get()
            ->keyBy('habit_id');

        $contentForDay = Content::query()
            ->with('user', 'post', 'project', 'thought', 'coverImage', 'embedImages')
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startUtc, $endUtc])
            ->visibleForIndex(Auth::user())
            ->orderBy('created_at')
            ->get();

        $contentForDay->transform(function (Content $content) use ($excerptGenerator): Content {
            if (in_array($content->content_type, [ContentType::POST, ContentType::PROJECT], true)) {
                $content->setAttribute(
                    'excerpt',
                    $content->body ? $excerptGenerator->generate($content->body) : null,
                );
            }

            return $content;
        });

        $activityFeed = $this->buildActivityFeed($user, $excerptGenerator);

        return view('user.profile', [
            'profileUser' => $user,
            'day' => $day,
            'isOwner' => $isOwner,
            'metrics' => $metrics,
            'habits' => $habits,
            'metricEntries' => $metricEntries,
            'habitEntries' => $habitEntries,
            'contentForDay' => $contentForDay,
            'activityFeed' => $activityFeed,
        ]);
    }

    public function dayFragment(User $user, string $date, ContentExcerptGenerator $excerptGenerator): View
    {
        $day = CarbonImmutable::createFromFormat('Y-m-d', $date, $user->timezone);
        abort_unless($day instanceof CarbonImmutable, 404);

        [$startUtc, $endUtc] = $this->dayRangeUtc($day);

        $isOwner = Auth::id() === $user->id;

        $metrics = $user->metrics()
            ->visibleTo(Auth::user())
            ->orderBy('name')
            ->get();

        $habits = $user->habits()
            ->visibleTo(Auth::user())
            ->orderBy('name')
            ->get();

        $metricEntries = MetricEntry::query()
            ->where('user_id', $user->id)
            ->where('date', $day->format('Y-m-d'))
            ->whereIn('metric_id', $metrics->pluck('id'))
            ->get()
            ->keyBy('metric_id');

        $habitEntries = HabitEntry::query()
            ->where('user_id', $user->id)
            ->where('date', $day->format('Y-m-d'))
            ->whereIn('habit_id', $habits->pluck('id'))
            ->get()
            ->keyBy('habit_id');

        $contentForDay = Content::query()
            ->with('user', 'post', 'project', 'thought', 'coverImage', 'embedImages')
            ->where('user_id', $user->id)
            ->whereBetween('created_at', [$startUtc, $endUtc])
            ->visibleForIndex(Auth::user())
            ->orderBy('created_at')
            ->get();

        $contentForDay->transform(function (Content $content) use ($excerptGenerator): Content {
            if (in_array($content->content_type, [ContentType::POST, ContentType::PROJECT], true)) {
                $content->setAttribute(
                    'excerpt',
                    $content->body ? $excerptGenerator->generate($content->body) : null,
                );
            }

            return $content;
        });

        return view('user._day', [
            'profileUser' => $user,
            'day' => $day,
            'isOwner' => $isOwner,
            'metrics' => $metrics,
            'habits' => $habits,
            'metricEntries' => $metricEntries,
            'habitEntries' => $habitEntries,
            'contentForDay' => $contentForDay,
        ]);
    }

    /** @return array{0: \Illuminate\Support\Carbon, 1: \Illuminate\Support\Carbon} */
    private function dayRangeUtc(CarbonImmutable $dayInUserTz): array
    {
        $start = $dayInUserTz->startOfDay();
        $end = $dayInUserTz->endOfDay();

        return [
            \Illuminate\Support\Facades\Date::instance($start->utc()),
            \Illuminate\Support\Facades\Date::instance($end->utc()),
        ];
    }

    /** @return \Illuminate\Support\Collection<int, Content> */
    private function buildActivityFeed(User $user, ContentExcerptGenerator $excerptGenerator)
    {
        $base = Content::query()
            ->where('user_id', $user->id)
            ->visibleForIndex(Auth::user());

        $posts = (clone $base)
            ->with('coverImage', 'post')
            ->where('content_type', ContentType::POST->value)
            ->latest()
            ->take(10)
            ->get();

        $projects = (clone $base)
            ->with('coverImage', 'project')
            ->where('content_type', ContentType::PROJECT->value)
            ->latest()
            ->take(10)
            ->get();

        $thoughts = (clone $base)
            ->with('embedImages', 'thought')
            ->where('content_type', ContentType::THOUGHT->value)
            ->latest()
            ->take(10)
            ->get();

        $posts->transform(function (Content $content) use ($excerptGenerator): Content {
            $content->setAttribute(
                'excerpt',
                $content->body ? $excerptGenerator->generate($content->body) : null,
            );

            return $content;
        });

        $projects->transform(function (Content $content) use ($excerptGenerator): Content {
            $content->setAttribute(
                'excerpt',
                $content->body ? $excerptGenerator->generate($content->body) : null,
            );

            return $content;
        });

        return $posts
            ->concat($projects)
            ->concat($thoughts)
            ->sortByDesc('created_at')
            ->values();
    }
}
