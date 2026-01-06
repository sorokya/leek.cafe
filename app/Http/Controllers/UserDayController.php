<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HabitEntry;
use App\Models\MetricEntry;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class UserDayController extends Controller
{
    public function store(Request $request, User $user, string $date): RedirectResponse|Response
    {
        abort_unless(Auth::id() === $user->id, 403);

        $day = CarbonImmutable::createFromFormat('Y-m-d', $date, $user->timezone);
        abort_unless($day instanceof CarbonImmutable, 404);

        $dayString = $day->format('Y-m-d');

        $validated = $request->validate([
            'metrics' => ['array'],
            'metrics.*' => ['nullable'],
            'habits' => ['array'],
            'habits.*' => ['nullable'],
        ]);

        $metricValues = array_key_exists('metrics', $validated) && is_array($validated['metrics'])
            ? $validated['metrics']
            : [];

        $habitDoneMap = array_key_exists('habits', $validated) && is_array($validated['habits'])
            ? $validated['habits']
            : [];

        $this->persistMetrics($user, $dayString, $metricValues);
        $this->persistHabits($user, $dayString, $habitDoneMap);

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return to_route('user.profile.date', [$user, $dayString]);
    }

    public function storeMetrics(Request $request, User $user, string $date): RedirectResponse
    {
        abort_unless(Auth::id() === $user->id, 403);

        $day = CarbonImmutable::createFromFormat('Y-m-d', $date, $user->timezone);
        abort_unless($day instanceof CarbonImmutable, 404);

        $dayString = $day->format('Y-m-d');

        $validated = $request->validate([
            'metrics' => ['array'],
            'metrics.*' => ['nullable'],
        ]);

        $values = array_key_exists('metrics', $validated) && is_array($validated['metrics'])
            ? $validated['metrics']
            : [];

        $this->persistMetrics($user, $dayString, $values);

        return to_route('user.profile.date', [$user, $dayString]);
    }

    public function storeHabits(Request $request, User $user, string $date): RedirectResponse
    {
        abort_unless(Auth::id() === $user->id, 403);

        $day = CarbonImmutable::createFromFormat('Y-m-d', $date, $user->timezone);
        abort_unless($day instanceof CarbonImmutable, 404);

        $dayString = $day->format('Y-m-d');

        $validated = $request->validate([
            'habits' => ['array'],
            'habits.*' => ['nullable'],
        ]);

        $doneMap = array_key_exists('habits', $validated) && is_array($validated['habits'])
            ? $validated['habits']
            : [];

        $this->persistHabits($user, $dayString, $doneMap);

        return to_route('user.profile.date', [$user, $dayString]);
    }

    /** @param array<int|string, mixed> $values */
    private function persistMetrics(User $user, string $dayString, array $values): void
    {
        $metrics = $user->metrics()->get()->keyBy('id');

        foreach ($values as $metricId => $rawValue) {
            $metricId = (int) $metricId;
            $metric = $metrics->get($metricId);
            if (! $metric) {
                continue;
            }

            if ($rawValue === null || (is_string($rawValue) && trim($rawValue) === '')) {
                MetricEntry::query()
                    ->where('user_id', $user->id)
                    ->where('metric_id', $metricId)
                    ->where('date', $dayString)
                    ->delete();

                continue;
            }

            $value = is_string($rawValue) || is_numeric($rawValue)
                ? (string) $rawValue
                : null;

            if ($value === null) {
                continue;
            }

            $optionList = $metric->optionList();
            if ($optionList !== [] && ! in_array($value, $optionList, true)) {
                throw ValidationException::withMessages([
                    'metrics.' . $metricId => 'Invalid option selected.',
                ]);
            }

            if ($optionList === []) {
                if (! is_numeric($value)) {
                    throw ValidationException::withMessages([
                        'metrics.' . $metricId => 'Must be a number.',
                    ]);
                }

                $asFloat = (float) $value;

                if ($metric->min !== null && $asFloat < (float) $metric->min) {
                    throw ValidationException::withMessages([
                        'metrics.' . $metricId => 'Must be at least ' . $metric->min . '.',
                    ]);
                }

                if ($metric->max !== null && $asFloat > (float) $metric->max) {
                    throw ValidationException::withMessages([
                        'metrics.' . $metricId => 'Must be at most ' . $metric->max . '.',
                    ]);
                }
            }

            MetricEntry::query()->updateOrCreate([
                'user_id' => $user->id,
                'metric_id' => $metricId,
                'date' => $dayString,
            ], [
                'value' => $value,
            ]);
        }
    }

    /** @param array<int|string, mixed> $doneMap */
    private function persistHabits(User $user, string $dayString, array $doneMap): void
    {
        $habits = $user->habits()->get()->keyBy('id');

        foreach ($habits as $habitId => $habit) {
            $isDone = array_key_exists((string) $habitId, $doneMap);

            if (! $isDone) {
                HabitEntry::query()
                    ->where('user_id', $user->id)
                    ->where('habit_id', (int) $habitId)
                    ->where('date', $dayString)
                    ->delete();

                continue;
            }

            HabitEntry::query()->updateOrCreate([
                'user_id' => $user->id,
                'habit_id' => (int) $habitId,
                'date' => $dayString,
            ], [
                'done' => true,
            ]);
        }
    }
}
