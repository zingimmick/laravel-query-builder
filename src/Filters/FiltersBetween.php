<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Zing\QueryBuilder\Exceptions\ParameterException;

class FiltersBetween extends FiltersExact
{
    /**
     * @param mixed $value
     * @param Expression|string $property
     */
    protected function withPropertyConstraint(Builder $query, $value, $property): Builder
    {
        if (! is_array($value) || count($value) !== 2) {
            throw ParameterException::tooFewElementsForBetweenExpression();
        }

        return $query->whereBetween($property, $value);
    }
}
