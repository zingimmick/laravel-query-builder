<?php

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Filter;

class FilterTest extends TestCase
{
    public function test_filter()
    {
        $filter = Filter::exact('order_number', 'number')->withCast(CastType::CAST_BOOLEAN);
        self::assertTrue($filter->hasCast());
        self::assertTrue($filter->isForProperty('order_number'));
        self::assertSame('number', $filter->getColumn());
    }
}