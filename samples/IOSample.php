<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Samples;

class IOSample
{
    public function __construct(
        public string $uri,
        public string $sql
    ) {
    }
}
