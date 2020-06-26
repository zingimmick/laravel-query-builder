<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Concerns\NestedRelation;
use Zing\QueryBuilder\Contracts\Filter;

class FiltersExact implements Filter
{
    use NestedRelation;

    protected $relationConstraints = [];

    public function apply(Builder $query, $value, $property): Builder
    {
        if ($property instanceof Expression) {
            return $this->withPropertyConstraint($query, $value, $property);
        }

        if ($this->isRelationProperty($query, $property)) {
            return $this->withRelationConstraint($query, $value, $property);
        }

        return $this->withPropertyConstraint($query, $value, $property);
    }

    protected function withPropertyConstraint(Builder $query, $value, $property)
    {
        if (is_array($value)) {
            return $query->whereIn($property, $value);
        }

        return $query->where($property, '=', $value);
    }

    protected function isRelationProperty(Builder $query, string $property): bool
    {
        if (! Str::contains($property, '.')) {
            return false;
        }

        if (in_array($property, $this->relationConstraints, true)) {
            return false;
        }

        return ! Str::startsWith($property, $query->getModel()->getTable() . '.');
    }

    protected function withRelationConstraint($query, $value, string $property)
    {
        [$relation, $property] = $this->resolveNestedRelation($property);

        return $query->whereHas(
            $relation,
            function ($query) use ($value, $property): void {
                $this->relationConstraints[] = $property = $query->getModel()->getTable() . '.' . $property;

                $this->apply($query, $value, $property);
            }
        );
    }
}
