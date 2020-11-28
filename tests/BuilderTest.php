<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use ReflectionClass;
use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Exceptions\ParameterException;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;
use Zing\QueryBuilder\Tests\Models\User;

class BuilderTest extends TestCase
{
    public function testExact(): void
    {
        request()->merge(
            [
                'name' => '2',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name'))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('name'),
                function ($query, $value) {
                    return $query->where('name', $value);
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testCast(): void
    {
        User::factory()->times(2)->create(
            [
                'is_visible' => true,
            ]
        );
        User::factory()->times(3)->create(
            [
                'is_visible' => false,
            ]
        );
        request()->merge(
            [
                'is_visible' => 'true',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();

        self::assertSame(2, $actual);
        request()->merge(
            [
                'is_visible' => 'true',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible'))
            ->count();

        self::assertSame(2, $actual);
        request()->merge(
            [
                'is_visible' => 'false',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();
        self::assertSame(3, $actual);
        request()->merge(
            [
                'is_visible' => 'false',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible'))
            ->count();
        self::assertSame(3, $actual);

        request()->merge(
            [
                'is_visible' => '1',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();

        self::assertSame(2, $actual);
        request()->merge(
            [
                'is_visible' => '0',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();
        self::assertSame(3, $actual);
    }

    public function testPartial(): void
    {
        request()->merge(
            [
                'name' => '2',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::partial('name'))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('name'),
                function ($query, $value) {
                    return $query->where('name', 'like', "%{$value}%");
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testPartialNull(): void
    {
        request()->merge(
            [
                'name' => null,
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::partial('name'))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('name'),
                function ($query, $value) {
                    return $query->where('name', 'like', "%{$value}%");
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testExactRelation(): void
    {
        Order::factory()->times(3)->create();
        $user = User::factory()->create();
        Order::factory()->times(2)->create(
            [
                'user_id' => $user->getKey(),
            ]
        );
        request()->merge(
            [
                'name' => $user->name,
            ]
        );
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(Filter::exact('name', 'user.name'))
            ->count();
        self::assertSame(2, $actual);
    }

    public function testExactQualified(): void
    {
        $user = User::factory()->create();
        request()->merge(
            [
                'name' => $user->name,
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name', 'users.name'))
            ->count();
        self::assertSame(1, $actual);
    }

    public function testScope(): void
    {
        User::factory()->times(2)->create(
            [
                'is_visible' => true,
            ]
        );
        User::factory()->times(3)->create(
            [
                'is_visible' => false,
            ]
        );
        request()->merge(
            [
                'is_visible' => 'true',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::scope('is_visible', 'visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();
        self::assertSame(2, $actual);
        request()->merge(
            [
                'is_visible' => 'false',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::scope('is_visible', 'visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();

        self::assertSame(3, $actual);
    }

    public function testExactArray(): void
    {
        request()->merge(
            [
                'name' => '1,2',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name'))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('name'),
                function ($query, $value) {
                    $value = explode(',', $value);

                    return $query->whereIn('name', $value);
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testPartialArray(): void
    {
        request()->merge(
            [
                'name' => '1,2',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::partial('name'))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('name'),
                function ($query, $value) {
                    $value = explode(',', $value);

                    return $query->where(
                        function ($query) use ($value) {
                            collect($value)->each(
                                function ($item) use ($query): void {
                                    $query->orWhere('name', 'like', "%{$item}%");
                                }
                            );

                            return $query;
                        }
                    );
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testPartialCastArray(): void
    {
        request()->merge(
            [
                'name' => [1, 2],
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::partial('name')->withCast(CastType::CAST_ARRAY))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('name'),
                function ($query, $value) {
                    return $query->where(
                        function ($query) use ($value) {
                            collect($value)->each(
                                function ($item) use ($query): void {
                                    $query->orWhere('name', 'like', "%{$item}%");
                                }
                            );

                            return $query;
                        }
                    );
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testPartialCastStringToArray(): void
    {
        request()->merge(
            [
                'name' => '1,2',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::partial('name')->withCast(CastType::CAST_ARRAY))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('name'),
                function ($query, $value) {
                    $value = explode(',', $value);

                    return $query->where(
                        function ($query) use ($value) {
                            collect($value)->each(
                                function ($item) use ($query): void {
                                    $query->orWhere('name', 'like', "%{$item}%");
                                }
                            );

                            return $query;
                        }
                    );
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testSkipCastStringToArray(): void
    {
        request()->merge(
            [
                'name' => '1,2',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::partial('name')->withCast(CastType::CAST_STRING))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('name'),
                function ($query, $value) {
                    return $query->where('name', 'like', "%{$value}%");
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testPartialRelation(): void
    {
        Order::factory()->times(3)->create();
        $user = User::factory()->create();
        Order::factory()->times(2)->create(
            [
                'user_id' => $user->getKey(),
            ]
        );
        request()->merge(
            [
                'name' => $user->name,
            ]
        );
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(Filter::partial('name', 'user.name'))
            ->count();
        self::assertSame(2, $actual);
    }

    public function testCustom(): void
    {
        Order::factory()->times(3)->create();
        request()->merge(
            [
                'id' => 3,
            ]
        );
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(
                [
                    Filter::custom('id', new LessThan()),
                ]
            )
            ->count();
        self::assertSame(2, $actual);
    }

    public function testCustomDefault(): void
    {
        Order::factory()->times(3)->create();
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(
                [
                    Filter::custom('id', new LessThan())->default(3),
                ]
            )
            ->count();
        self::assertSame(2, $actual);
        $actual = QueryBuilder::fromBuilder(
            Order::class,
            request()->merge(
                [
                    'id' => 2,
                ]
            )
        )
            ->enableFilters(
                [
                    Filter::custom('id', new LessThan())->default(3),
                ]
            )
            ->count();
        self::assertSame(1, $actual);
    }

    public function testIgnore(): void
    {
        Order::factory()->times(3)->create();
        request()->merge(
            [
                'id' => [1, 2, 3],
            ]
        );
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(
                [
                    Filter::exact('id'),
                ]
            )
            ->count();
        self::assertSame(3, $actual);
        request()->merge(
            [
                'id' => [1, 2, 3],
            ]
        );
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(
                [
                    Filter::exact('id')->ignore(1),
                ]
            )
            ->count();
        self::assertSame(2, $actual);
    }

    public function testCallback(): void
    {
        Order::factory()->times(3)->create();
        request()->merge(
            [
                'id' => 3,
            ]
        );
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(
                [
                    Filter::callback(
                        'id',
                        function ($query, $value, string $property) {
                            return $query->where($property, '<', $value);
                        }
                    ),
                ]
            )
            ->count();
        self::assertSame(2, $actual);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCastInteger(): void
    {
        $filter = Filter::scope('name')->withCast(CastType::CAST_INTEGER);
        $method = (new ReflectionClass($filter))->getMethod('resolveValueForFiltering');
        $method->setAccessible(true);
        self::assertSame(1, $method->invokeArgs($filter, ['1']));
    }

    public function testSort(): void
    {
        request()->merge(
            [
                'asc' => 'name',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableSorts(['name'])
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('asc'),
                function ($query) {
                    return $query->orderBy('name');
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testSortCustom(): void
    {
        request()->merge(
            [
                'asc' => 'custom_name',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableSorts(
                [
                    'custom_name' => 'name',
                ]
            )
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('asc'),
                function ($query) {
                    return $query->orderBy('name');
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testPerPage(): void
    {
        $builder = QueryBuilder::fromBuilder(User::class, request());
        self::assertSame(config('query-builder.per_page.default'), $builder->paginate()->perPage());
        $perPage = 10;
        request()->merge(
            [
                'per_page' => $perPage,
            ]
        );
        $builder = QueryBuilder::fromBuilder(User::class, request());
        self::assertSame($perPage, $builder->paginate()->perPage());
    }

    public function testSimplePaginate(): void
    {
        $builder = QueryBuilder::fromBuilder(User::class, request());
        self::assertSame(config('query-builder.per_page.default'), $builder->simplePaginate()->perPage());
        $perPage = 10;
        request()->merge(
            [
                'per_page' => $perPage,
            ]
        );
        $builder = QueryBuilder::fromBuilder(User::class, request());
        self::assertSame($perPage, $builder->simplePaginate()->perPage());
    }

    public function testBetween(): void
    {
        request()->merge(
            [
                'id' => '2,3',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::between('id'))
            ->toSql();
        $expected = User::query()
            ->when(
                request()->input('id'),
                function ($query, $value) {
                    return $query->whereBetween('id', explode(',', $value));
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testBetweenException(): void
    {
        request()->merge(
            [
                'id' => '2',
            ]
        );
        self::expectException(ParameterException::class);
        QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::between('id'))
            ->toSql();
    }

    public function testBetweenRelation(): void
    {
        request()->merge(
            [
                'user_id' => '2,3',
            ]
        );
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(Filter::between('user_id', 'user.id'))
            ->toSql();
        $expected = Order::query()
            ->whereHas(
                'user',
                function ($query) {
                    return $query->when(
                        request()->input('user_id'),
                        function ($query, $value) {
                            return $query->whereBetween((new User())->getModel()->qualifyColumn('id'), explode(',', $value));
                        }
                    );
                }
            )
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function testBetweenDateTime(): void
    {
        request()->merge(
            [
                'created_between' => '2020-01-02,2020-03-04',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::betweenDateTime('created_between', 'created_at'));
        $expected = User::query()
            ->when(
                request()->input('created_between'),
                function ($query, $value) {
                    [$min, $max] = explode(',', $value);
                    if (is_string($min)) {
                        $startAt = Carbon::parse($min);
                        if ($startAt->toDateString() === $min) {
                            $startAt->startOfDay();
                        }
                    }

                    if (is_string($max)) {
                        $endAt = Carbon::parse($max);
                        if ($endAt->toDateString() === $max) {
                            $endAt->endOfDay();
                        }
                    }

                    return $query->whereBetween('created_at', [$startAt, $endAt]);
                }
            );
        self::assertEqualsCanonicalizing($expected->getBindings(), $actual->getBindings());
        self::assertSame($expected->toSql(), $actual->toSql());
    }

    public function testBetweenDateTimeInstance(): void
    {
        request()->merge(
            [
                'created_between' => [Carbon::yesterday(), Carbon::today()],
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::betweenDateTime('created_between', 'created_at'));
        $expected = User::query()
            ->when(
                request()->input('created_between'),
                function ($query, $value) {
                    [$min, $max] = $value;
                    if (is_string($min)) {
                        $startAt = Carbon::parse($min);
                        if ($startAt->toDateString() === $min) {
                            $startAt->startOfDay();
                        }
                    } else {
                        $startAt = $min;
                    }

                    if (is_string($max)) {
                        $endAt = Carbon::parse($max);
                        if ($endAt->toDateString() === $max) {
                            $endAt->endOfDay();
                        }
                    } else {
                        $endAt = $max;
                    }

                    return $query->whereBetween('created_at', [$startAt, $endAt]);
                }
            );
        self::assertEqualsCanonicalizing($expected->getBindings(), $actual->getBindings());
        self::assertSame($expected->toSql(), $actual->toSql());
    }

    public function testBetweenDate(): void
    {
        request()->merge(
            [
                'created_between' => '2020-01-02,2020-03-04',
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::betweenDate('created_between', 'created_at'));
        $expected = User::query()
            ->when(
                request()->input('created_between'),
                function ($query, $value) {
                    $value = explode(',', $value);

                    return $query->whereBetween(
                        'created_at',
                        array_map(
                            function ($dateTime) {
                                if (is_string($dateTime)) {
                                    return Carbon::parse($dateTime)->format('Y-m-d');
                                }

                                if ($dateTime instanceof DateTimeInterface) {
                                    return $dateTime->format('Y-m-d');
                                }

                                return $dateTime;
                            },
                            $value
                        )
                    );
                }
            );
        self::assertEqualsCanonicalizing($expected->getBindings(), $actual->getBindings());
        self::assertSame($expected->toSql(), $actual->toSql());
    }

    public function testBetweenDateMixed(): void
    {
        request()->merge(
            [
                'created_between' => [Carbon::yesterday()->getTimestamp(), Carbon::now()->getTimestamp()],
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::betweenDate('created_between', 'created_at'));
        $expected = User::query()
            ->when(
                request()->input('created_between'),
                function ($query, $value) {
                    return $query->whereBetween(
                        'created_at',
                        array_map(
                            function ($dateTime) {
                                if (is_string($dateTime)) {
                                    return Carbon::parse($dateTime)->format('Y-m-d');
                                }

                                if ($dateTime instanceof DateTimeInterface) {
                                    return $dateTime->format('Y-m-d');
                                }

                                return $dateTime;
                            },
                            $value
                        )
                    );
                }
            );
        self::assertEqualsCanonicalizing($expected->getBindings(), $actual->getBindings());
        self::assertSame($expected->toSql(), $actual->toSql());
    }

    public function testBetweenDateInstance(): void
    {
        request()->merge(
            [
                'created_between' => [Carbon::yesterday(), Carbon::now()],
            ]
        );
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::betweenDate('created_between', 'created_at'));
        $expected = User::query()
            ->when(
                request()->input('created_between'),
                function ($query, $value) {
                    return $query->whereBetween(
                        'created_at',
                        array_map(
                            function ($dateTime) {
                                if (is_string($dateTime)) {
                                    return Carbon::parse($dateTime)->format('Y-m-d');
                                }

                                if ($dateTime instanceof DateTimeInterface) {
                                    return $dateTime->format('Y-m-d');
                                }

                                return $dateTime;
                            },
                            $value
                        )
                    );
                }
            );
        self::assertEqualsCanonicalizing($expected->getBindings(), $actual->getBindings());
        self::assertSame($expected->toSql(), $actual->toSql());
    }
}
