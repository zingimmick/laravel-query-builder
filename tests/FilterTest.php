<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\QueryConfiguration;
use Zing\QueryBuilder\Tests\Models\User;

class FilterTest extends TestCase
{
    public function testFilter(): void
    {
        $filter = Filter::exact('order_number', 'number')->withCast(CastType::BOOLEAN);
        self::assertTrue($filter->hasCast());
        self::assertTrue($filter->isForProperty('order_number'));
        self::assertSame('number', $filter->getColumn());
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
                function ($query, $value) {
                    $value = explode('|', $value);

                    return $query->whereIn('name', $value);
                }
            )
            ->toSql();
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name')->delimiter('|'))
            ->toSql();
        self::assertSame($expected, $actual);
        QueryConfiguration::setDelimiter('|');
        $actual = QueryBuilder::fromBuilder(User::class, request())
            ->enableFilters(Filter::exact('name'))
            ->toSql();
        self::assertSame($expected, $actual);
    }
}
