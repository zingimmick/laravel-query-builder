<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;

class FiltersPartial extends FiltersExact
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @param \Illuminate\Database\Query\Expression|string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function withPropertyConstraint(Builder $query, $value, $property): Builder
    {
        if (is_array($value)) {
            return $query->where(
                function ($query) use ($value, $property): void {
                    foreach ($value as $singleValue) {
                        $query->orWhere($property, 'like', sprintf('%%%s%%', $singleValue));
                    }
                }
            );
        }

        return $query->where($property, 'like', sprintf('%%%s%%', $value));
    }
}
