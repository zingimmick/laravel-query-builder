<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Exceptions;

class ParameterException extends Exception
{
    public static function unsupportedFilterWithDefaultValueForSearch(): self
    {
        return new self('unsupported filter with default value for search');
    }

    public static function tooFewElementsForBetweenExpression(): self
    {
        return new self('Too few elements for between expression, at least 2 elements expected');
    }

    public static function unsupportedFilterWithDefaultValueForTypedFilter(): self
    {
        return new self('unsupported filter with default value for typed filter');
    }
}
