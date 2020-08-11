<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Builders;

use Zing\QueryBuilder\Concerns\Queryable;

class MongodbQueryBuilder extends \Jenssegers\Mongodb\Eloquent\Builder
{
    use Queryable;
}
