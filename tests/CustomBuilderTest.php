<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

/**
 * @internal
 */
final class CustomBuilderTest extends TestCase
{
    public function testScope(): void
    {
        $this->assertSame(
            Order::query()->whereNumberLike('test')->toSql(),
            QueryBuilder::fromBuilder(Order::class, request())->whereNumberLike('test')->toSql()
        );
    }
}
