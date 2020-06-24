<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;

class FiltersPartial extends FiltersExact
{
    protected function withPropertyConstraint(Builder $query, $value, $property)
    {
        if (is_array($value)) {
            return $query->where(
                function ($query) use ($value, $property): void {
                    foreach ($value as $partialValue) {
                        $query->orWhere($property, 'like', "%{$partialValue}%");
                    }
                }
            );
        }

        return $query->where($property, 'like', "%{$value}%");
    }
}
