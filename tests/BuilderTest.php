<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use ReflectionClass;
use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;

class BuilderTest extends TestCase
{
    public function testExact(): void
    {
        request()->merge(['name' => '2']);
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
        factory(User::class)->times(2)->create(
            [
                'is_visible' => true,
            ]
        );
        factory(User::class)->times(3)->create(
            [
                'is_visible' => false,
            ]
        );
        request()->merge(['is_visible' => 'true']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();

        self::assertSame(2, $actual);
        request()->merge(['is_visible' => 'true']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible'))
            ->count();

        self::assertSame(2, $actual);
        request()->merge(['is_visible' => 'false']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();
        self::assertSame(3, $actual);
        request()->merge(['is_visible' => 'false']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible'))
            ->count();
        self::assertSame(3, $actual);

        request()->merge(['is_visible' => '1']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();

        self::assertSame(2, $actual);
        request()->merge(['is_visible' => '0']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();
        self::assertSame(3, $actual);
    }

    public function testPartial(): void
    {
        request()->merge(['name' => '2']);
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
        request()->merge(['name' => null]);
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
        factory(Order::class)->times(3)->create();
        $user = factory(User::class)->create();
        factory(Order::class)->times(2)->create(
            [
                'user_id' => $user->getKey(),
            ]
        );
        request()->merge(['name' => $user->name]);
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(Filter::exact('name', 'user.name'))
            ->count();
        self::assertSame(2, $actual);
    }

    public function testExactQualified(): void
    {
        $user = factory(User::class)->create();
        request()->merge(['name' => $user->name]);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name', 'users.name'))
            ->count();
        self::assertSame(1, $actual);
    }

    public function testScope(): void
    {
        factory(User::class)->times(2)->create(
            [
                'is_visible' => true,
            ]
        );
        factory(User::class)->times(3)->create(
            [
                'is_visible' => false,
            ]
        );
        request()->merge(['is_visible' => 'true']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::scope('is_visible', 'visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();
        self::assertSame(2, $actual);
        request()->merge(['is_visible' => 'false']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::scope('is_visible', 'visible')->withCast(CastType::CAST_BOOLEAN))
            ->count();

        self::assertSame(3, $actual);
    }

    public function testExactArray(): void
    {
        request()->merge(['name' => '1,2']);
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
        request()->merge(['name' => '1,2']);
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
        request()->merge(['name' => [1, 2]]);
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
        request()->merge(['name' => '1,2']);
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

    public function testPartialRelation(): void
    {
        factory(Order::class)->times(3)->create();
        $user = factory(User::class)->create();
        factory(Order::class)->times(2)->create(
            [
                'user_id' => $user->getKey(),
            ]
        );
        request()->merge(['name' => $user->name]);
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(Filter::partial('name', 'user.name'))
            ->count();
        self::assertSame(2, $actual);
    }

    public function testCustom(): void
    {
        factory(Order::class)->times(3)->create();
        request()->merge(['id' => 3]);
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
        factory(Order::class)->times(3)->create();
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(
                [
                    Filter::custom('id', new LessThan())->default(3),
                ]
            )
            ->count();
        self::assertSame(2, $actual);
        $actual = QueryBuilder::fromBuilder(Order::class, request()->merge(['id' => 2]))
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
        factory(Order::class)->times(3)->create();
        request()->merge(['id' => [1, 2, 3]]);
        $actual = QueryBuilder::fromBuilder(Order::class, request())
            ->enableFilters(
                [
                    Filter::exact('id'),
                ]
            )
            ->count();
        self::assertSame(3, $actual);
        request()->merge(['id' => [1, 2, 3]]);
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
        factory(Order::class)->times(3)->create();
        request()->merge(['id' => 3]);
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
        request()->merge(['asc' => 'name']);
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
        request()->merge(['asc' => 'custom_name']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableSorts(['custom_name' => 'name'])
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
        $perPage = 10;
        request()->merge(['per_page' => $perPage]);
        $builder = QueryBuilder::fromBuilder(User::class, request());
        self::assertSame($perPage, $builder->paginate()->perPage());
    }
}
