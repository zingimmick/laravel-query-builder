<?php

declare(strict_types=1);

use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

QueryBuilder::fromBuilder(Order::class, $request)
    ->enableFilters(Filter::partial('content')->withCast(CastType::ORIGINAL))
    ->simplePaginate();
