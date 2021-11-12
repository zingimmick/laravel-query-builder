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
    /**
     * @var bool
     */
    protected $autoRelationConstraints = true;

    /**
     * @param bool $autoRelationConstraints
     */
    public function __construct(bool $autoRelationConstraints = true)
    {
        $this->autoRelationConstraints = $autoRelationConstraints;
    }

    /**
     * @param mixed $value
     * @param string|\Illuminate\Database\Query\Expression $property
     */
    public function apply(Builder $query, $value, $property): Builder
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
     * @param \Illuminate\Database\Query\Expression|string $property
     */
    protected function withPropertyConstraint(Builder $query, $value, $property): Builder
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

    /**
     * @param mixed $value
     */
    protected function withRelationConstraint(Builder $query, $value, string $property): Builder
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
