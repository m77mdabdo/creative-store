<?php

namespace App\Support\Shipping;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Turns a rate's "min to max delivery days" into real dates the customer can
 * plan around: "Today", "Tomorrow", "Sun, 15 Nov" or "15 – 18 Nov".
 *
 * Days are counted in the store's timezone, courier days off (Friday by
 * default) are skipped, and orders placed after the daily cut-off start
 * counting from the next day, the way large marketplaces promise delivery.
 */
final class DeliveryPromise
{
    private function __construct(
        public readonly CarbonImmutable $from,
        public readonly CarbonImmutable $to,
        public readonly int $minDays,
        public readonly int $maxDays,
        public readonly ?int $minutesToCutoff,
    ) {
    }

    public static function make(?int $minDays, ?int $maxDays, ?CarbonInterface $orderedAt = null): self
    {
        $min = max(0, (int) $minDays);
        $max = max($min, (int) ($maxDays ?? $min));

        $timezone = (string) config('store.delivery.timezone', 'Asia/Riyadh');
        $now = CarbonImmutable::instance($orderedAt ?? now())->setTimezone($timezone);
        $cutoff = $now->startOfDay()->setTime((int) config('store.delivery.cutoff_hour', 14), 0);

        // Work handed over after the cut-off (or on a day off) leaves the next working day.
        $start = $now->greaterThanOrEqualTo($cutoff) ? $now->addDay()->startOfDay() : $now->startOfDay();
        $start = self::nextWorkingDay($start);

        $minutesToCutoff = $min <= 1 && $now->lessThan($cutoff) && ! self::isDayOff($now)
            ? (int) ceil($now->diffInMinutes($cutoff, true))
            : null;

        return new self(
            self::addWorkingDays($start, $min),
            self::addWorkingDays($start, $max),
            $min,
            $max,
            $minutesToCutoff,
        );
    }

    /**
     * The promise as it was made for an order, from its stored dates.
     */
    public static function forOrder(?CarbonInterface $from, ?CarbonInterface $to): ?self
    {
        if (! $from && ! $to) {
            return null;
        }

        $timezone = (string) config('store.delivery.timezone', 'Asia/Riyadh');
        $from = CarbonImmutable::instance($from ?? $to)->setTimezone($timezone)->startOfDay();
        $to = CarbonImmutable::instance($to ?? $from)->setTimezone($timezone)->startOfDay();

        return new self($from, $to->max($from), 0, 0, null);
    }

    /** "Today", "Tomorrow", "Sun, 15 Nov" or "15 – 18 Nov". */
    public function label(): string
    {
        if ($this->from->isSameDay($this->to)) {
            return self::dayName($this->from);
        }

        if ($this->from->isSameDay(self::today()) && $this->to->isSameDay(self::today()->addDay())) {
            return __('shipping.eta.today_or_tomorrow');
        }

        $sameMonth = $this->from->isSameMonth($this->to);

        return __('shipping.eta.range', [
            'from' => $this->from->locale(app()->getLocale())->translatedFormat($sameMonth ? 'j' : 'j M'),
            'to'   => $this->to->locale(app()->getLocale())->translatedFormat('j M'),
        ]);
    }

    /** "Arrives tomorrow" / "Arrives 15 – 18 Nov". */
    public function headline(): string
    {
        $when = $this->label();

        if (in_array($when, [__('shipping.eta.today'), __('shipping.eta.tomorrow'), __('shipping.eta.today_or_tomorrow')], true)) {
            $when = mb_strtolower($when);
        }

        return __('shipping.eta.arrives', ['when' => $when]);
    }

    /** "5 – 7 days", "Within a day", "Same day". */
    public function window(): string
    {
        return match (true) {
            $this->maxDays === 0                   => __('shipping.eta.window_same_day'),
            $this->minDays === $this->maxDays      => trans_choice('shipping.eta.window_exact', $this->maxDays, ['count' => $this->maxDays]),
            default                                => __('shipping.eta.window_range', ['min' => $this->minDays, 'max' => $this->maxDays]),
        };
    }

    /** "Order within 2 h 15 min" while a same-day or next-day promise still holds. */
    public function cutoffHint(): ?string
    {
        if ($this->minutesToCutoff === null || $this->minutesToCutoff > 6 * 60) {
            return null;
        }

        $hours = intdiv($this->minutesToCutoff, 60);
        $minutes = $this->minutesToCutoff % 60;

        return __('shipping.eta.order_within', [
            'time' => $hours > 0
                ? __('shipping.eta.hours_minutes', ['h' => $hours, 'm' => $minutes])
                : __('shipping.eta.minutes', ['m' => max(1, $minutes)]),
        ]);
    }

    public function isLate(): bool
    {
        return self::today()->greaterThan($this->to);
    }

    public function toArray(): array
    {
        return [
            'from'        => $this->from->toDateString(),
            'to'          => $this->to->toDateString(),
            'label'       => $this->label(),
            'headline'    => $this->headline(),
            'window'      => $this->window(),
            'cutoff_hint' => $this->cutoffHint(),
        ];
    }

    private static function dayName(CarbonImmutable $day): string
    {
        $today = self::today();

        return match (true) {
            $day->isSameDay($today)          => __('shipping.eta.today'),
            $day->isSameDay($today->addDay()) => __('shipping.eta.tomorrow'),
            default                          => $day->locale(app()->getLocale())
                ->translatedFormat(app()->getLocale() === 'ar' ? 'l، j M' : 'D, j M'),
        };
    }

    private static function today(): CarbonImmutable
    {
        return CarbonImmutable::now((string) config('store.delivery.timezone', 'Asia/Riyadh'))->startOfDay();
    }

    private static function addWorkingDays(CarbonImmutable $day, int $days): CarbonImmutable
    {
        for ($i = 0; $i < $days; $i++) {
            $day = self::nextWorkingDay($day->addDay());
        }

        return $day;
    }

    private static function nextWorkingDay(CarbonImmutable $day): CarbonImmutable
    {
        for ($guard = 0; $guard < 7 && self::isDayOff($day); $guard++) {
            $day = $day->addDay();
        }

        return $day;
    }

    private static function isDayOff(CarbonInterface $day): bool
    {
        return in_array($day->dayOfWeek, (array) config('store.delivery.days_off', [5]), true);
    }
}
