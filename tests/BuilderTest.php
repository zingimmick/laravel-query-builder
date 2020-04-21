<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;

class BuilderTest extends TestCase
{
    public function test_searchable(): void
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

    public function test_exact(): void
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

    public function test_cast(): void
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
        request()->merge(['is_visible' => 'false']);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('is_visible')->withCast(CastType::CAST_BOOLEAN))
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

    public function test_partial(): void
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

    public function test_partial_null(): void
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

    public function test_exact_relation(): void
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

    public function test_exact_qualified(): void
    {
        $user = factory(User::class)->create();
        request()->merge(['name' => $user->name]);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name', 'users.name'))
            ->count();
        self::assertSame(1, $actual);
    }

    public function test_scope(): void
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

    public function test_exact_array(): void
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

    public function test_partial_array(): void
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

    public function test_partial_relation(): void
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

    public function test_searchable_relation(): void
    {
        factory(Order::class)->times(3)->create();
        $user = factory(User::class)->create();
        factory(Order::class)->times(2)->create(
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

    public function test_custom(): void
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

    public function test_custom_default(): void
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
    }

    public function test_ignore(): void
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

    public function test_callback(): void
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
}
