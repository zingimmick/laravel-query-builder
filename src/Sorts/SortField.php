<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Contracts\Sort;

class SortField implements Sort
{
    /**
     * @param string $property
     */
    public function apply(Builder $query, bool $descending, string $property): Builder
    {
        return $query->orderBy($property, $descending ? 'desc' : 'asc');
    }
}
