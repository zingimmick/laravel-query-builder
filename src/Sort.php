<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Sorts\FieldSort;

class Sort
{
    protected \Closure|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Expression|string $column;

    protected string $defaultDirection;

    /**
     * Sort constructor.
     *
     * @param \Closure|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Expression|string|null $column
     */
    public function __construct(
        protected string $property,
        protected Contracts\Sort $sort,
        $column
    ) {
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

    public function getColumn(): \Closure|Builder|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Expression|string
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
