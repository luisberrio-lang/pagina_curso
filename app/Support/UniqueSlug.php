<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class UniqueSlug
{
    public static function for(Builder $query, string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'elemento';

        for ($suffix = 1; $suffix <= 10000; $suffix++) {
            $candidate = $suffix === 1 ? $base : $base.'-'.$suffix;
            $exists = (clone $query)
                ->when($ignoreId, fn (Builder $builder) => $builder->whereKeyNot($ignoreId))
                ->where('slug', $candidate)
                ->exists();

            if (! $exists) {
                return $candidate;
            }
        }

        return $base.'-'.Str::lower(Str::random(12));
    }
}
