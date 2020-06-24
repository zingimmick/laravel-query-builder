<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Contracts\Filter;

class FiltersCallback implements Filter
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function apply(Builder $query, $value, string $property): Builder
    {
        return call_user_func($this->callback, $query, $value, $property);
    }
}
