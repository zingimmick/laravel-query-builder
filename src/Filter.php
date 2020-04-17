<?php

namespace Zing\QueryBuilder;

use Zing\QueryBuilder\Filters\FiltersExact;
use Zing\QueryBuilder\Filters\FiltersPartial;
use Zing\QueryBuilder\Filters\FiltersScope;

class Filter
{
    /** @var \Zing\QueryBuilder\Contracts\Filter */
    protected $filter;

    /** @var string */
    protected $property;

    /** @var string */
    protected $column;

    public function __construct(string $property, $filter, $column = null)
    {
        $this->property = $property;

        $this->filter = $filter;

        $this->column = $column ?? $property;
    }

    public function filter($model, $value)
    {
        if ($value === null) {
            return $model;
        }

        return $this->filter->apply($model, $value, $this->column);
    }

    /**
     * 通过另一个字段查询.
     *
     * @param string $property
     * @param \Illuminate\Database\Query\Expression|string|null $column
     *
     * @return \Zing\QueryBuilder\Filter
     */
    public static function exact(string $property, $column = null): self
    {
        return new static($property, new FiltersExact(), $column);
    }

    /**
     * 通过字段模糊查询.
     *
     * @param string $property
     * @param string|null $column
     *
     * @return \Zing\QueryBuilder\Filter
     */
    public static function partial(string $property, $column = null): self
    {
        return new static($property, new FiltersPartial(), $column);
    }

    /**
     * 通过作用域查询.
     *
     * @param string $property
     * @param string|null $column
     *
     * @return \Zing\QueryBuilder\Filter
     */
    public static function scope(string $property, $column = null): self
    {
        return new static($property, new FiltersScope(), $column);
    }

    /**
     * 自定义过滤器.
     *
     * @param string $property
     * @param \Zing\QueryBuilder\Contracts\Filter $filterClass
     * @param string|null $column
     *
     * @return \Zing\QueryBuilder\Filter
     */
    public static function custom(string $property, Contracts\Filter $filterClass, $column = null): self
    {
        return new static($property, $filterClass, $column);
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function isForProperty(string $property): bool
    {
        return $this->property === $property;
    }

    public function getColumn(): string
    {
        return $this->column;
    }
}
