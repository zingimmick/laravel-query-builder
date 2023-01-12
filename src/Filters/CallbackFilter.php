<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Contracts\Filter;

class CallbackFilter implements Filter
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function apply(Builder $query, mixed $value, \Illuminate\Database\Query\Expression|string $property): Builder
    {
        \call_user_func($this->callback, $query, $value, $property);

        return $query;
    }
}
