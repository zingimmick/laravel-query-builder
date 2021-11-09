<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Contracts\Sort;

class FieldSort implements Sort
{
    /**
     * @param string|\Illuminate\Database\Query\Expression $property
     */
    public function apply(Builder $query, bool $descending,  $property): Builder
    {
        return $query->orderBy($property, $descending ? 'desc' : 'asc');
    }
}
