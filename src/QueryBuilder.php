<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Concerns\Queryable;

class QueryBuilder extends Builder
{
    use Queryable;
}
