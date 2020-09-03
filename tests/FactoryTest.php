<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Builders\QueryBuilder;
use Zing\QueryBuilder\Concerns\Queryable;
use Zing\QueryBuilder\QueryBuilderFactory;
use Zing\QueryBuilder\Tests\Models\Order;

class FactoryTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertInstanceOf(QueryBuilder::class, QueryBuilderFactory::create(Order::class, request()));
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
