<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;
use Zing\QueryBuilder\Tests\Models\User;

class SearchableTest extends TestCase
{
    public function testSearchable(): void
    {
        request()->merge(['search' => '1', 'a' => '2']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->searchable(['b', 'c'])
            ->enableFilters('a')
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('search'),
                function ($query, $search) {
                    return $query->where(
                        function ($query) use ($search) {
                            return $query->orWhere('b', 'like', "%{$search}%")
                                ->orWhere('c', 'like', "%{$search}%");
                        }
                    );
                }
            )
            ->when(
                request()->input('a'),
                function ($query, $value) {
                    return $query->where('a', $value);
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testSearchableRelation(): void
    {
        Order::factory()->times(3)->create();
        $user = User::factory()->create();
        Order::factory()->times(2)->create(
            [
                'user_id' => $user->getKey(),
            ]
        );
        request()->merge(['search' => $user->name]);
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->searchable('user.name')
            ->count();
        self::assertSame(2, $actual);
    }

    public function testSearchableForBlank(): void
    {
        Order::factory()->times(3)->create();
        $user = User::factory()->create();
        Order::factory()->times(2)->create(
            [
                'user_id' => $user->getKey(),
            ]
        );
        request()->merge(['search' => '']);
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->searchable('user.name')
            ->count();
        self::assertSame(5, $actual);
    }
}
