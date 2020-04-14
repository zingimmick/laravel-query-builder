<?php

namespace Zing\QueryBuilder\Contracts;

interface Filter
{
    public function apply($query, $value, string $property);
}
