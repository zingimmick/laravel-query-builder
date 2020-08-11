<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Builders;

use Jenssegers\Mongodb\Eloquent\Builder;
use Zing\QueryBuilder\Concerns\Queryable;

class MongodbQueryBuilder extends Builder
{
    use Queryable;
}
