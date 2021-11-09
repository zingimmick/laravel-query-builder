<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Sorts\FieldSort;

class Sort
{
    protected string $property;

    /**
     * @var \Zing\QueryBuilder\Contracts\Sort
     */
    protected $sort;

    /**
     * @var \Closure|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Expression|string
     */
    protected $column;

    protected string $defaultDirection;

    /**
     * Sort constructor.
     *
     * @param \Closure|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Expression|string|null $column
     */
    public function __construct(string $property, Contracts\Sort $sort, $column)
    {
        $this->property = $property;
        $this->sort = $sort;
        $this->column = $column ?? $property;
    }

    /**
     * @param \Closure|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Expression|string|null $column
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
     * @return \Closure|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Expression|string
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
