<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DateFilter extends ExactFilter
{
    /**
     * @param mixed $value
     * @param \Illuminate\Database\Query\Expression|string $property
     */
    protected function withPropertyConstraint(Builder $query, $value, $property): Builder
    {
        $formatter = static function ($value) {
            return $value instanceof DateTimeInterface ? $value->format('Y-m-d') : $value;
        };
        if (\is_array($value)) {
            $value = array_map($formatter, $value);

            return $query->whereIn(DB::raw(sprintf('date(%s)', $property)), $value);
        }

        $value = $formatter($value);

        return $query->whereDate($property, $value);
    }
}
