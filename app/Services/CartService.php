<?php

namespace App\Services;

use App\Models\Course;
use App\Support\Money;

class CartService
{
    private const SESSION_KEY = 'cart.course_ids';

    public function ids(): array
    {
        $ids = session(self::SESSION_KEY, []);

        return array_values(array_unique(array_filter(array_map('intval', (array) $ids), fn ($id) => $id > 0)));
    }

    public function add(Course $course): bool
    {
        if (! $course->is_published || ! $course->hasCommercialPrice()) {
            return false;
        }

        $ids = $this->ids();
        if (! in_array($course->getKey(), $ids, true)) {
            $ids[] = $course->getKey();
            session()->put(self::SESSION_KEY, $ids);
        }

        return true;
    }

    public function remove(Course $course): void
    {
        session()->put(
            self::SESSION_KEY,
            array_values(array_filter($this->ids(), fn ($id) => $id !== $course->getKey())),
        );
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return count($this->snapshot()['items']);
    }

    public function snapshot(bool $pruneInvalid = true): array
    {
        $ids = $this->ids();
        $courses = Course::query()->whereIn('id', $ids)->get()->keyBy('id');
        $items = [];
        $validIds = [];
        $totalMinor = 0;

        foreach ($ids as $id) {
            $course = $courses->get($id);
            if (! $course || ! $course->is_published || ! $course->hasCommercialPrice()) {
                continue;
            }

            $price = $course->currentPrice();
            $lineMinor = Money::toMinorUnits($price);
            $validIds[] = $id;
            $totalMinor += $lineMinor;
            $items[] = [
                'course' => $course,
                'quantity' => 1,
                'unit_price' => $price,
                'currency' => $course->currency(),
                'line_total' => Money::fromMinorUnits($lineMinor),
            ];
        }

        if ($pruneInvalid && $ids !== $validIds) {
            session()->put(self::SESSION_KEY, $validIds);
        }

        $total = Money::fromMinorUnits($totalMinor);

        return [
            'items' => $items,
            'invalid_ids' => array_values(array_diff($ids, $validIds)),
            'subtotal' => $total,
            'total' => $total,
            'currency' => Money::currencyCode(),
            'formatted_total' => Money::format($total),
        ];
    }
}
