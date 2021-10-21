<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Exceptions;

class ParameterException extends Exception
{
    public static function tooFewElementsForBetweenExpression(): self
    {
        return new self('Too few elements for between expression, at least 2 elements expected');
    }
}
