<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FiltersDate extends FiltersExact
{
    /**
     * @param mixed $value
     * @param \Illuminate\Database\Query\Expression|string $property
     */
    protected function withPropertyConstraint(Builder $query, $value, $property): Builder
    {
        if (is_array($value)) {
            $value = array_map(
                function ($value) {
                if ($value instanceof DateTimeInterface) {
                    return $value->format('Y-m-d');
                }

                return $value;
            },
                $value
            );

            return $query->whereIn(DB::raw(sprintf('date(%s)', $property)), $value);
        }

        if ($value instanceof DateTimeInterface) {
            $value = $value->format('Y-m-d');
        }

        return $query->whereDate($property, $value);
    }
}
