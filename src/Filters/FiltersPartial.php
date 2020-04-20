<?php

namespace Zing\QueryBuilder\Filters;

class FiltersPartial extends FiltersExact
{
    public function apply($query, $value, string $property)
    {
        if ($this->isRelationProperty($query, $property)) {
            return $this->withRelationConstraint($query, $value, $property);
        }

        if (is_array($value)) {
            return $query->where(
                function ($query) use ($value, $property) {
                    foreach ($value as $partialValue) {
                        $query->orWhere($property, 'like', "%{$partialValue}%");
                    }
                }
            );
        }

        return $query->where($property, 'like', "%{$value}%");
    }
}
