<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Enums;

class CastType
{
    public const STRING = 'string';

    public const BOOLEAN = 'boolean';

    public const ARRAY = 'array';

    public const INTEGER = 'integer';

    /**
     * @deprecated use CastType::STRING instead
     */
    public const CAST_STRING = self::STRING;

    /**
     * @deprecated use CastType::BOOLEAN instead
     */
    public const CAST_BOOLEAN = self::BOOLEAN;

    /**
     * @deprecated use CastType::ARRAY instead
     */
    public const CAST_ARRAY = self::ARRAY;

    /**
     * @deprecated use CastType::INTEGER instead
     */
    public const CAST_INTEGER = self::INTEGER;
}
