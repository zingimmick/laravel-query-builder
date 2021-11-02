<?php

namespace Zing\QueryBuilder;

use function config;

class Paginator
{
    /** @var string */
    private $name;
    /** @var int */
    private $default;

    /**
     * @param string $name
     * @param int|null $default
     */
    public function __construct(string $name, int $default = null)
    {
        $this->name = $name;
        $this->default = $default ?: (int) config('query-builder.per_page.value');
    }

    public static function name(string $name, int $default = null): Paginator
    {
        return new self($name, $default);
    }

    public function default(int $default): Paginator
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDefault(): int
    {
        return $this->default;
    }
}
