<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Sort;
use Zing\QueryBuilder\Tests\Models\Order;
use Zing\QueryBuilder\Tests\Models\User;

/**
 * @internal
 */
final class SortTest extends TestCase
{
    use WithFaker;

    public function test(): void
    {
        $filter = Sort::field('order_number', 'number');
        self::assertTrue($filter->isForProperty('order_number'));
        self::assertSame('number', $filter->getColumn());
    }

    public function testSortExpression(): void
    {
        request()->merge([
            'desc' => 'registered_at',
        ]);
        $expected = Order::query()
            ->orderBy(
                User::query()->whereColumn(
                    User::query()->getModel()->getQualifiedKeyName(),
                    Order::query()->getModel()->qualifyColumn('user_id')
                )->select(User::query()->getModel()->qualifyColumn('created_at')),
                'desc'
            )->toSql();
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableSorts(
                [Sort::field(
                    'registered_at',
                    User::query()->whereColumn(
                        User::query()->getModel()->getQualifiedKeyName(),
                        Order::query()->getModel()->qualifyColumn('user_id')
                    )->select(User::query()->getModel()->qualifyColumn('created_at'))
                ),
                ]
            )
            ->toSql();

        self::assertSame($expected, $actual);
    }
}
