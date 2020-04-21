<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Contracts;

interface Filter
{
    public function apply($query, $value, string $property);
}
