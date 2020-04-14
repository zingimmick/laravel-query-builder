<?php

namespace Zing\QueryBuilder\Filters;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Contracts\Filter;

class FiltersScope implements Filter
{
    public function apply($query, $value, string $property)
    {
        $scope = Str::camel($property);
        $values = Arr::wrap($value);

        return $query->{$scope}(...$values);
    }
}
