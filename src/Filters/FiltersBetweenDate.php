<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class FiltersBetweenDate extends FiltersBetween
{
    public function apply(Builder $query, $value, $property): Builder
    {
        return parent::apply(
            $query,
            array_map(
                function ($dateTime) {
                    if (is_string($dateTime)) {
                        return Carbon::parse($dateTime)->format('Y-m-d');
                    }

                    if ($dateTime instanceof DateTimeInterface) {
                        return $dateTime->format('Y-m-d');
                    }

                    return $dateTime;
                },
                $value
            ),
            $property
        );
    }
}
