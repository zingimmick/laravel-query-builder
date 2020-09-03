<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Concerns\Queryable;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\QueryBuilderFactory;
use Zing\QueryBuilder\Tests\Builders\OrderBuilder;
use Zing\QueryBuilder\Tests\Builders\OrderQueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

class FactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = app(QueryBuilderFactory::class);
        self::assertInstanceOf(QueryBuilder::class, $factory->create(Order::class, request()));
        $factory->queryBy(OrderBuilder::class, OrderQueryBuilder::class);
        self::assertInstanceOf(OrderQueryBuilder::class, $factory->create(Order::class, request()));
    }

    public function provideBuilders()
    {
        return [
            [QueryBuilder::class],
        ];
    }

    /**
     * @dataProvider provideBuilders
     *
     * @param $builder
     */
    public function testBuilder($builder): void
    {
        self::assertContains(Queryable::class, trait_uses_recursive($builder));
    }
}
