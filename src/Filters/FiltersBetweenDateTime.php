<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class FiltersBetweenDateTime extends FiltersBetween
{
    public function apply(Builder $query, $value, $property): Builder
    {
        $min = head($value);
        $max = last($value);
        if (is_string($min)) {
            $startAt = Carbon::parse($min);
            if ($startAt->toDateString() === $min) {
                $startAt->startOfDay();
            }
        } else {
            $startAt = $min;
        }

        if (is_string($max)) {
            $endAt = Carbon::parse($max);
            if ($endAt->toDateString() === $max) {
                $endAt->endOfDay();
            }
        } else {
            $endAt = $max;
        }

        return parent::apply($query, [$startAt, $endAt], $property);
    }
}
