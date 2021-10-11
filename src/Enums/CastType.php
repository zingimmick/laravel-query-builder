<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Enums;

class CastType
{
    /**
     * @var string
     */
    public const STRING = 'string';

    /**
     * @var string
     */
    public const BOOLEAN = 'boolean';

    /**
     * @var string
     */
    public const ARRAY = 'array';

    /**
     * @var string
     */
    public const INTEGER = 'integer';

    /**
     * @deprecated use CastType::STRING instead
     *
     * @var string
     */
    public const CAST_STRING = self::STRING;

    /**
     * @deprecated use CastType::BOOLEAN instead
     *
     * @var string
     */
    public const CAST_BOOLEAN = self::BOOLEAN;

    /**
     * @deprecated use CastType::ARRAY instead
     *
     * @var string
     */
    public const CAST_ARRAY = self::ARRAY;

    /**
     * @deprecated use CastType::INTEGER instead
     *
     * @var string
     */
    public const CAST_INTEGER = self::INTEGER;
}
