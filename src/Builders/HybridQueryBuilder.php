<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Builders;

use Jenssegers\Mongodb\Helpers\EloquentBuilder;
use Zing\QueryBuilder\Concerns\Queryable;

class HybridQueryBuilder extends EloquentBuilder
{
    use Queryable;
}
