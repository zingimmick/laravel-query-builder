<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

class CustomBuilderTest extends TestCase
{
    public function testScope(): void
    {
        self::assertSame(
            Order::query()->whereNumberLike('test')->toSql(),
            QueryBuilder::fromBuilder(Order::class, request())->whereNumberLike('test')->toSql()
        );
    }
}
