<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class FiltersBetweenDateTime extends FiltersBetween
{
    public function apply(Builder $query, $value, $property): Builder
    {
        $min = Arr::first($value);
        $max = Arr::last($value);
        $startAt = Carbon::parse($min);
        if ($startAt->toDateString() === $min) {
            $startAt->startOfDay();
        }

        $endAt = Carbon::parse($max);
        if ($endAt->toDateString() === $max) {
            $endAt->endOfDay();
        }

        return parent::apply($query, [$startAt, $endAt], $property);
    }
}
