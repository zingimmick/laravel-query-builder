<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Filter
{
    public function apply(
        Builder $query,
        mixed $value,
        \Illuminate\Database\Query\Expression|string $property
    ): Builder;
}
