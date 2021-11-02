<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

class FactoryTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertSame(
            Order::query()->whereNumberLike('test')->toSql(),
            QueryBuilder::fromBuilder(Order::class, request())->whereNumberLike('test')->toSql()
        );
    }

    /**
     * @return array<int, array<class-string<\Zing\QueryBuilder\QueryBuilder>>>
     */
    public function provideBuilders(): array
    {
        return [[QueryBuilder::class]];
    }
}
