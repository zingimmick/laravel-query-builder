<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Contracts\Filter;

class LessThan implements Filter
{
    public function apply(Builder $query, mixed $value, \Illuminate\Database\Query\Expression|string $property): Builder
    {
        return $query->where($property, '<', $value);
    }
}
