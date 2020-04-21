<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;

class FiltersPartial extends FiltersExact
{
    public function apply($query, $value, string $property): Builder
    {
        if ($this->isRelationProperty($query, $property)) {
            return $this->withRelationConstraint($query, $value, $property);
        }

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
