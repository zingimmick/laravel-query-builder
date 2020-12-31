<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Sort
{
    public function apply(Builder $query, bool $descending, string $property): Builder;
}
