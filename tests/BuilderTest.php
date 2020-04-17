<?php

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;

class BuilderTest extends TestCase
{
    public function test_searchable()
    {
        request()->merge(['search' => '1', 'a' => '2']);
        $actual = QueryBuilder::for(User::class, request())
            ->searchable(['b', 'c'])
            ->addFilters('a')
            ->toSql();
        $expected = User::query()
            ->when(request()->input('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    return $query->orWhere('b', 'like', "%${search}%")
                        ->orWhere('c', 'like', "%${search}%");
                });
            })
            ->when(request()->input('a'), function ($query, $value) {
                return $query->where('a', $value);
            })
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function test_exact()
    {
        request()->merge(['name' => '2']);
        $actual = QueryBuilder::for(User::class, request())
            ->addFilters(Filter::exact('name'))
            ->toSql();
        $expected = User::query()
            ->when(request()->input('name'), function ($query, $value) {
                return $query->where('name', $value);
            })
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function test_cast()
    {
        factory(User::class)->times(2)->create([
            'is_visible' => true,
        ]);
        factory(User::class)->times(3)->create([
            'is_visible' => false,
        ]);
        request()->merge(['is_visible' => 'true']);
        $actual = QueryBuilder::for(User::class, request())
            ->withCasts([
                'is_visible' => QueryBuilder::CAST_BOOLEAN,
            ])
            ->addFilters(Filter::exact('is_visible'))
            ->count();

        self::assertSame(2, $actual);
        request()->merge(['is_visible' => 'false']);
        $actual = QueryBuilder::for(User::class, request())
            ->withCasts([
                'is_visible' => QueryBuilder::CAST_BOOLEAN,
            ])
            ->addFilters(Filter::exact('is_visible'))
            ->count();
        self::assertSame(3, $actual);

        request()->merge(['is_visible' => '1']);
        $actual = QueryBuilder::for(User::class, request())
            ->withCasts([
                'is_visible' => QueryBuilder::CAST_BOOLEAN,
            ])
            ->addFilters(Filter::exact('is_visible'))
            ->count();

        self::assertSame(2, $actual);
        request()->merge(['is_visible' => '0']);
        $actual = QueryBuilder::for(User::class, request())
            ->withCasts([
                'is_visible' => QueryBuilder::CAST_BOOLEAN,
            ])
            ->addFilters(Filter::exact('is_visible'))
            ->count();
        self::assertSame(3, $actual);
    }

    public function test_partial()
    {
        request()->merge(['name' => '2']);
        $actual = QueryBuilder::for(User::class, request())
            ->addFilters(Filter::partial('name'))
            ->toSql();
        $expected = User::query()
            ->when(request()->input('name'), function ($query, $value) {
                return $query->where('name', 'like', "%${value}%");
            })
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function test_partial_null()
    {
        request()->merge(['name' => null]);
        $actual = QueryBuilder::for(User::class, request())
            ->addFilters(Filter::partial('name'))
            ->toSql();
        $expected = User::query()
            ->when(request()->input('name'), function ($query, $value) {
                return $query->where('name', 'like', "%${value}%");
            })
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function test_exact_relation()
    {
        factory(Order::class)->times(3)->create();
        $user = factory(User::class)->create();
        factory(Order::class)->times(2)->create([
            'user_id' => $user->getKey(),
        ]);
        request()->merge(['name' => $user->name]);
        $actual = QueryBuilder::for(Order::class, request())
            ->addFilters(Filter::exact('name', 'user.name'))
            ->count();
        self::assertSame(2, $actual);
    }

    public function test_exact_qualified()
    {
        $user = factory(User::class)->create();
        request()->merge(['name' => $user->name]);
        $actual = QueryBuilder::for(User::class, request())
            ->addFilters(Filter::exact('name', 'users.name'))
            ->count();
        self::assertSame(1, $actual);
    }

    public function test_scope()
    {
        factory(User::class)->times(2)->create([
            'is_visible' => true,
        ]);
        factory(User::class)->times(3)->create([
            'is_visible' => false,
        ]);
        request()->merge(['is_visible' => 'true']);
        $actual = QueryBuilder::for(User::class, request())
            ->withCasts([
                'is_visible' => QueryBuilder::CAST_BOOLEAN,
            ])
            ->addFilters(Filter::scope('is_visible', 'visible'))
            ->count();
        self::assertSame(2, $actual);
        request()->merge(['is_visible' => 'false']);
        $actual = QueryBuilder::for(User::class, request())
            ->withCasts([
                'is_visible' => QueryBuilder::CAST_BOOLEAN,
            ])
            ->addFilters(Filter::scope('is_visible', 'visible'))
            ->count();
        self::assertSame(3, $actual);
    }

    public function test_exact_array()
    {
        request()->merge(['name' => '1,2']);
        $actual = QueryBuilder::for(User::class, request())
            ->addFilters(Filter::exact('name'))
            ->toSql();
        $expected = User::query()
            ->when(request()->input('name'), function ($query, $value) {
                $value = explode(',', $value);

                return $query->whereIn('name', $value);
            })
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function test_partial_array()
    {
        request()->merge(['name' => '1,2']);
        $actual = QueryBuilder::for(User::class, request())
            ->addFilters(Filter::partial('name'))
            ->toSql();
        $expected = User::query()
            ->when(request()->input('name'), function ($query, $value) {
                $value = explode(',', $value);

                return $query->where(function ($query) use ($value) {
                    collect($value)->each(function ($item) use ($query) {
                        $query->orWhere('name', 'like', "%${item}%");
                    });

                    return $query;
                });
            })
            ->toSql();
        self::assertSame($expected, $actual);
    }

    public function test_partial_relation()
    {
        factory(Order::class)->times(3)->create();
        $user = factory(User::class)->create();
        factory(Order::class)->times(2)->create([
            'user_id' => $user->getKey(),
        ]);
        request()->merge(['name' => $user->name]);
        $actual = QueryBuilder::for(Order::class, request())
            ->addFilters(Filter::partial('name', 'user.name'))
            ->count();
        self::assertSame(2, $actual);
    }

    public function test_searchable_relation()
    {
        factory(Order::class)->times(3)->create();
        $user = factory(User::class)->create();
        factory(Order::class)->times(2)->create([
            'user_id' => $user->getKey(),
        ]);
        request()->merge(['search' => $user->name]);
        $actual = QueryBuilder::for(Order::class, request())
            ->searchable('user.name')
            ->count();
        self::assertSame(2, $actual);
    }

    public function test_custom()
    {
        factory(Order::class)->times(3)->create();
        request()->merge(['id' => 3]);
        $actual = QueryBuilder::for(Order::class, request())
            ->addFilters([
                Filter::custom('id', new LessThan()),
            ])
            ->count();
        self::assertSame(2, $actual);
    }
}

class LessThan implements \Zing\QueryBuilder\Contracts\Filter
{
    public function apply($query, $value, string $property)
    {
        return $query->where($property, '<', $value);
    }
}
