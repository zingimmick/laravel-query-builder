<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Samples;

class IOSample
{
    /**
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $sql;

    public function __construct(string $uri, string $sql)
    {
        $this->uri = $uri;
        $this->sql = $sql;
    }
}
