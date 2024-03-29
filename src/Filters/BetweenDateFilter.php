<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class BetweenDateFilter extends BetweenFilter
{
    /**
     * @param string $property
     */
    public function apply(Builder $query, mixed $value, $property): Builder
    {
        $value = array_map(
            static function ($dateTime) {
                if (\is_string($dateTime)) {
                    return Carbon::parse($dateTime)->format('Y-m-d');
                }

                if ($dateTime instanceof \DateTimeInterface) {
                    return $dateTime->format('Y-m-d');
                }

                return $dateTime;
            },
            $value
        );

        return parent::apply($query, $value, $property);
    }
}
