<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HabitEntry;
use App\Models\MetricEntry;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class UserDayController extends Controller
{
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

        $metrics = $user->metrics()->get()->keyBy('id');

        $values = array_key_exists('metrics', $validated) && is_array($validated['metrics'])
            ? $validated['metrics']
            : [];

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

        $habits = $user->habits()->get()->keyBy('id');

        $doneMap = array_key_exists('habits', $validated) && is_array($validated['habits'])
            ? $validated['habits']
            : [];

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

        return to_route('user.profile.date', [$user, $dayString]);
    }
}
