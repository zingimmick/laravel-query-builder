<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Concerns\NestedRelation;
use Zing\QueryBuilder\Contracts\Filter;

class ExactFilter implements Filter
{
    use NestedRelation;

    /**
     * @var string[]
     */
    protected $relationConstraints = [];

    public function __construct(
        protected bool $autoRelationConstraints = true
    ) {
    }

    /**
     * @param mixed $value
     * @param string|\Illuminate\Database\Query\Expression $property
     */
    public function apply(Builder $query, mixed $value, $property): Builder
    {
        if ($property instanceof Expression) {
            return $this->withPropertyConstraint($query, $value, $property);
        }

        if ($this->autoRelationConstraints && $this->isRelationProperty($query, $property)) {
            return $this->withRelationConstraint($query, $value, $property);
        }

        return $this->withPropertyConstraint($query, $value, $property);
    }

    /**
     * @param mixed $value
     */
    protected function withPropertyConstraint(Builder $query, $value, Expression|string $property): Builder
    {
        if (\is_array($value)) {
            return $query->whereIn($property, $value);
        }

        return $query->where($property, '=', $value);
    }

    protected function isRelationProperty(Builder $query, string $property): bool
    {
        if (! Str::contains($property, '.')) {
            return false;
        }

        if (\in_array($property, $this->relationConstraints, true)) {
            return false;
        }

        return ! Str::startsWith($property, $query->getModel()->getTable() . '.');
    }

    protected function withRelationConstraint(Builder $query, mixed $value, string $property): Builder
    {
        [$relation, $property] = $this->resolveNestedRelation($property);

        return $query->whereHas(
            $relation,
            function ($query) use ($value, $property): void {
                $property = $query->getModel()
                    ->getTable() . '.' . $property;
                $this->relationConstraints[] = $property;

                $this->apply($query, $value, $property);
            }
        );
    }
}
