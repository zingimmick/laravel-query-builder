<?php

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Contracts\Filter;

class FiltersExact implements Filter
{
    protected $relationConstraints = [];

    public function apply($query, $value, string $property)
    {
        if ($this->isRelationProperty($query, $property)) {
            return $this->withRelationConstraint($query, $value, $property);
        }

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

        if (Str::startsWith($property, $query->getModel()->getTable() . '.')) {
            return false;
        }

        return true;
    }

    protected function withRelationConstraint($query, $value, string $property)
    {
        [$relation, $property] = collect(explode('.', $property))
            ->pipe(
                function (Collection $parts) {
                    return [
                        $parts->except(count($parts) - 1)->map([Str::class, 'camel'])->implode('.'),
                        $parts->last(),
                    ];
                }
            );

        return $query->whereHas(
            $relation,
            function ($query) use ($value, $property) {
                $this->relationConstraints[] = $property = $query->getModel()->getTable() . '.' . $property;

                $this->apply($query, $value, $property);
            }
        );
    }
}
