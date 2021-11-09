<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Sort
{
    /**
     * @param string|\Illuminate\Database\Query\Expression $property
     */
    public function apply(Builder $query, bool $descending,  $property): Builder;
}
