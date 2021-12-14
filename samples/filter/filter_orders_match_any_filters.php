<?php

declare(strict_types=1);

use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

QueryBuilder::fromBuilder(Order::class, $request)
    ->enableFlaggedFilter([Filter::partial('number'), Filter::partial('user_name', 'user.name')])
    ->simplePaginate();
