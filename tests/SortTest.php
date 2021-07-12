<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\Sort;

class SortTest extends TestCase
{
    public function test(): void
    {
        $filter = Sort::field('order_number', 'number');
        self::assertTrue($filter->isForProperty('order_number'));
        self::assertSame('number', $filter->getColumn());
    }
}
