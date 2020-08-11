<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Builders\HybridQueryBuilder;
use Zing\QueryBuilder\Builders\MongodbQueryBuilder;
use Zing\QueryBuilder\Builders\QueryBuilder;
use Zing\QueryBuilder\QueryBuilderFactory;
use Zing\QueryBuilder\Tests\Models\Order;
use Zing\QueryBuilder\Tests\Models\Subject;
use Zing\QueryBuilder\Tests\Models\User;

class FactoryTest extends TestCase
{
    public function testCreate(): void
    {
        self::assertInstanceOf(HybridQueryBuilder::class, QueryBuilderFactory::create(User::class, request()));
        self::assertInstanceOf(QueryBuilder::class, QueryBuilderFactory::create(Order::class, request()));
        self::assertInstanceOf(MongodbQueryBuilder::class, QueryBuilderFactory::create(Subject::class, request()));
    }
}
