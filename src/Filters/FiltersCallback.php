<?php

namespace Zing\QueryBuilder\Filters;

use Zing\QueryBuilder\Contracts\Filter;

class FiltersCallback implements Filter
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function apply($query, $value, string $property)
    {
        return call_user_func($this->callback, $query, $value, $property);
    }
}
