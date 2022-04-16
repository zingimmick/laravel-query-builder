<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Enums;

class CastType
{
    public const ORIGINAL='original';
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
}
