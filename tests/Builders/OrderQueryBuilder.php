<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Builders;

use Zing\QueryBuilder\Concerns\Queryable;

class OrderQueryBuilder extends OrderBuilder
{
    use Queryable;
}
