<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Exceptions\ParameterException;

class BetweenFilter extends ExactFilter
{
    /**
     * @param mixed $value
     */
    protected function withPropertyConstraint(
        Builder $query,
        $value,
        \Illuminate\Database\Query\Expression|string $property
    ): Builder {
        if (! \is_array($value) || \count($value) !== 2) {
            throw ParameterException::tooFewElementsForBetweenExpression();
        }

        return $query->whereBetween($property, $value);
    }
}
