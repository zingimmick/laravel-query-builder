<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use function config;

class Paginator
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $default;

    public function __construct(?string $name = null, ?int $default = null)
    {
        $this->name = $name ?: QueryConfiguration::getPageName();
        $this->default = $default ?: QueryConfiguration::getPerPage();
    }

    public static function name(?string $name = null, ?int $default = null): self
    {
        return new self($name, $default);
    }

    public function default(int $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefault(): int
    {
        return $this->default;
    }
}
