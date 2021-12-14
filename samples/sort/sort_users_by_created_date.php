<?php

declare(strict_types=1);

use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Sort;
use Zing\QueryBuilder\Tests\Models\Order;

QueryBuilder::fromBuilder(Order::class, $request)
    ->enableSorts([Sort::field('created_date', 'created_at')])
    ->simplePaginate();
