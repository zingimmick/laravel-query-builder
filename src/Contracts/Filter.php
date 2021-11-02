<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Filter
{
    /**
     * @param mixed $value
     * @param \Illuminate\Database\Query\Expression|string $property
     */
    public function apply(Builder $query, $value, $property): Builder;
}
