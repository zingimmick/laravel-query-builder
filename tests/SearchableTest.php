<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Zing\QueryBuilder\Exceptions\ParameterException;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;
use Zing\QueryBuilder\Tests\Models\User;

/**
 * @internal
 */
final class SearchableTest extends TestCase
{
    use WithFaker;

    public function testSearchable(): void
    {
        request()->merge([
            'search' => '1',
            'a' => '2',
        ]);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->searchable(['b', 'c'])
            ->enableFilters('a')
            ->toSql();
        $expected = User::query()
            ->when(
                request()
                    ->input('search'),
                function ($query, $search): Builder {
                    return $query->where(
                        function ($query) use ($search) {
                            return $query->orWhere('b', 'like', sprintf('%%%s%%', $search))
                                ->orWhere('c', 'like', sprintf('%%%s%%', $search));
                        }
                    );
                }
            )
            ->when(request()->input('a'), function ($query, $value): Builder {
                return $query->where('a', $value);
            })
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testSearchableRelation(): void
    {
        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 3)
        );
        $user = User::query()->create([
            'name' => $this->faker->name(),
        ]);
        array_map(
            function () use ($user): void {
                Order::query()->create([
                    'user_id' => $user->getKey(),
                    'number' => $this->faker->randomNumber(),
                ]);
            },
            range(1, 2)
        );
        request()
            ->merge([
                'search' => $user->name,
            ]);
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->searchable('user.name')
            ->count();
        self::assertSame(2, $actual);
    }

    public function testSearchableForBlank(): void
    {
        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 3)
        );
        $user = User::query()->create([
            'name' => $this->faker->name(),
        ]);

        array_map(
            function () use ($user): void {
                Order::query()->create([
                    'user_id' => $user->getKey(),
                    'number' => $this->faker->randomNumber(),
                ]);
            },
            range(1, 2)
        );
        request()
            ->merge([
                'search' => '',
            ]);
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->searchable('user.name')
            ->count();
        self::assertSame(5, $actual);
    }

    public function testSearchableForNull(): void
    {
        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 3)
        );
        $user = User::query()->create([
            'name' => $this->faker->name(),
        ]);

        array_map(
            function () use ($user): void {
                Order::query()->create([
                    'user_id' => $user->getKey(),
                    'number' => $this->faker->randomNumber(),
                ]);
            },
            range(1, 2)
        );
        request()
            ->merge([
                'search' => null,
            ]);
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->searchable('user.name')
            ->count();
        self::assertSame(5, $actual);
    }

    public function testSearchableFilters(): void
    {
        request()->merge([
            'search' => '1',
        ]);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->searchable([Filter::exact('b'), 'c'])
            ->toSql();
        $expected = User::query()
            ->when(
                request()
                    ->input('search'),
                function ($query, $search): Builder {
                    return $query->where(
                        function ($query) use ($search) {
                            return $query->orWhere(function ($query) use ($search) {
                                return $query->where('b', $search);
                            })
                                ->orWhere('c', 'like', sprintf('%%%s%%', $search));
                        }
                    );
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('unsupported filter with default value for search');
        QueryBuilder::fromBuilder(User::class, request())
            ->searchable([Filter::exact('b')->default('test'), 'c'])
            ->toSql();
    }
}
