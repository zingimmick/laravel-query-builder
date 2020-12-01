<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use PHPUnit\Framework\TestCase;
use Zing\QueryBuilder\Enums\CastType;

class CastTypeTest extends TestCase
{
    public function testDeprecated(): void
    {
        self::assertSame(CastType::CAST_STRING, CastType::STRING);
        self::assertSame(CastType::CAST_INTEGER, CastType::INTEGER);
        self::assertSame(CastType::CAST_BOOLEAN, CastType::BOOLEAN);
        self::assertSame(CastType::CAST_ARRAY, CastType::ARRAY);
    }
}
