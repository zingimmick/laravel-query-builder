<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Contracts\Filter;

class FiltersScope implements Filter
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @param string $property
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $query, $value,  $property): Builder
    {
        $scope = Str::camel($property);
        $values = Arr::wrap($value);

        return $query->{$scope}(...$values);
    }
}
