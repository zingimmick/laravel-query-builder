<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Exceptions\ParameterException;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\QueryConfiguration;
use Zing\QueryBuilder\Tests\Models\User;

/**
 * @internal
 */
final class FilterTest extends TestCase
{
    public function testFilter(): void
    {
        $filter = Filter::exact('order_number', 'number')->withCast(CastType::BOOLEAN);
        $this->assertTrue($filter->hasCast());
        $this->assertTrue($filter->isForProperty('order_number'));
        $this->assertSame('number', $filter->getColumn());
    }

    public function testDelimiter(): void
    {
        request()->merge([
            'name' => '1|2',
        ]);
        $expected = User::query()
            ->when(
                request()
                    ->input('name'),
                static function ($query, $value): Builder {
                    $value = explode('|', $value);

                    return $query->whereIn('name', $value);
                }
            )
            ->toSql();
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name')->delimiter('|'))
            ->toSql();
        $this->assertSame($expected, $actual);
        QueryConfiguration::setDelimiter('|');
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name'))
            ->toSql();
        $this->assertSame($expected, $actual);
        QueryConfiguration::setDelimiter(',');
    }

    public function testTyped(): void
    {
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableTypedFilter('search_type', 'search', [Filter::exact('name')]);
        $expected = User::query();
        $this->assertSame($expected->toSql(), $actual->toSql());
        $this->assertSame($expected->getBindings(), $actual->getBindings());
        request()
            ->merge([
                'search_type' => 'name',
                'search' => '1',
            ]);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableTypedFilter('search_type', 'search', [Filter::exact('name')]);
        $expected = User::query()
            ->where(request()->input('search_type'), request()->input('search'));
        $this->assertSame($expected->toSql(), $actual->toSql());
        $this->assertSame($expected->getBindings(), $actual->getBindings());
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableTypedFilter('search_type', 'search', [Filter::partial('name')]);
        $expected = User::query()
            ->where(request()->input('search_type'), 'like', sprintf('%%%s%%', request()->input('search')));
        $this->assertSame($expected->toSql(), $actual->toSql());
        $this->assertSame($expected->getBindings(), $actual->getBindings());
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableTypedFilter('search_type', 'search', [Filter::partial('email'), Filter::partial('name')]);
        $expected = User::query()
            ->where(request()->input('search_type'), 'like', sprintf('%%%s%%', request()->input('search')));
        $this->assertSame($expected->toSql(), $actual->toSql());
        $this->assertSame($expected->getBindings(), $actual->getBindings());
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableTypedFilter('search_type', 'search', [Filter::exact('email')]);
        $expected = User::query();
        $this->assertSame($expected->toSql(), $actual->toSql());
        $this->assertSame($expected->getBindings(), $actual->getBindings());
        $this->expectException(ParameterException::class);

        QueryBuilder::fromBuilder(User::class, request())
            ->enableTypedFilter('search_type', 'search', [Filter::partial('name')->default('test')]);
    }

    public function testFlagged(): void
    {
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFlaggedFilter([Filter::exact('name')->default('foo')]);
        $expected = User::query()->where(
            static fn ($query) => $query->where(static fn ($query) => $query->where('name', 'foo'))
        );
        $this->assertSame($expected->toSql(), $actual->toSql());
        $this->assertSame($expected->getBindings(), $actual->getBindings());
        request()
            ->merge([
                'name' => '1',
                'email' => '2',
            ]);
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFlaggedFilter([Filter::partial('email'), Filter::partial('name')]);
        $expected = User::query()
            ->where(
                static fn ($query) => $query->orWhere(
                    static fn ($query) => $query->where('email', 'like', sprintf('%%%s%%', request()->input('email')))
                )->orWhere(static fn ($query) => $query->where(
                    'name',
                    'like',
                    sprintf('%%%s%%', request()->input('name'))
                ))
            );
        $this->assertSame($expected->toSql(), $actual->toSql());
        $this->assertSame($expected->getBindings(), $actual->getBindings());
    }
}
