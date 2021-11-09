<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Sorts\FieldSort;

class Sort
{
    /**
     * @var string
     */
    protected $property;

    /**
     * @var \Zing\QueryBuilder\Contracts\Sort
     */
    protected $sort;

    /**
     * @var string|\Illuminate\Database\Query\Expression
     */
    protected $column;

    /**
     * @var string
     */
    protected $defaultDirection;

    /**
     * Sort constructor.
     *
     * @param string|\Illuminate\Database\Query\Expression|null $column
     */
    public function __construct(string $property, Contracts\Sort $sort, $column)
    {
        $this->property = $property;
        $this->sort = $sort;
        $this->column = $column ?? $property;
    }

    /**
     * @param string|\Illuminate\Database\Query\Expression|null $column
     */
    public static function field(string $property, $column = null): self
    {
        return new self($property, new FieldSort(), $column);
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function isForProperty(string $property): bool
    {
        return $this->property === $property;
    }

    /**
     * @return string|\Illuminate\Database\Query\Expression
     */
    public function getColumn()
    {
        return $this->column;
    }

    public function asc(): self
    {
        $this->defaultDirection = 'asc';

        return $this;
    }

    public function desc(): self
    {
        $this->defaultDirection = 'desc';

        return $this;
    }

    public function hasDefaultDirection(): bool
    {
        return $this->defaultDirection !== null;
    }

    public function getDefaultDirection(): string
    {
        return $this->defaultDirection;
    }

    public function sort(Builder $query, string $direction): Builder
    {
        return $this->sort->apply($query, $direction === 'desc', $this->column);
    }
}
