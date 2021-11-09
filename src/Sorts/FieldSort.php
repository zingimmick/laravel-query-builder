<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Contracts\Sort;

class FieldSort implements Sort
{
    /**
     * @param \Closure|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Expression|string $property
     */
    public function apply(Builder $query, bool $descending, $property): Builder
    {
        return $query->orderBy($property, $descending ? 'desc' : 'asc');
    }
}
