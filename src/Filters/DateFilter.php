<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DateFilter extends ExactFilter
{
    /**
     * @param mixed $value
     */
    protected function withPropertyConstraint(
        Builder $query,
        $value,
        \Illuminate\Database\Query\Expression|string $property
    ): Builder {
        $formatter = static fn ($value) => $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value;
        if (\is_array($value)) {
            $value = array_map($formatter, $value);

            return $query->whereIn(DB::raw(sprintf('date(%s)', $property)), $value);
        }

        $value = $formatter($value);

        return $query->whereDate($property, $value);
    }
}
