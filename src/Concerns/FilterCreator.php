<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Zing\QueryBuilder\Contracts\Filter;
use Zing\QueryBuilder\Filters\FiltersBetween;
use Zing\QueryBuilder\Filters\FiltersBetweenDate;
use Zing\QueryBuilder\Filters\FiltersBetweenDateTime;
use Zing\QueryBuilder\Filters\FiltersCallback;
use Zing\QueryBuilder\Filters\FiltersDate;
use Zing\QueryBuilder\Filters\FiltersExact;
use Zing\QueryBuilder\Filters\FiltersPartial;
use Zing\QueryBuilder\Filters\FiltersScope;

trait FilterCreator
{
    /**
     * @var \Zing\QueryBuilder\Contracts\Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $property;

    /**
     * @var \Illuminate\Database\Query\Expression|string
     */
    protected $column;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $ignored;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var string|null
     */
    protected $cast;

    /**
     * @param \Zing\QueryBuilder\Contracts\Filter $filter
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public function __construct(string $property, $filter, $column = null)
    {
        $this->property = $property;

        $this->filter = $filter;
        $this->ignored = collect();
        $this->column = $column ?? $property;
    }

    /**
     * The input of property equals the column(or property if column is null).
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function exact(string $property, $column = null): self
    {
        return new self($property, new FiltersExact(), $column);
    }

    /**
     * The input of property in the column(or property if column is null).
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function partial(string $property, $column = null): self
    {
        return new self($property, new FiltersPartial(), $column);
    }

    /**
     * Specify a scope(property if column is null) that will execute when the filter is requested.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function scope(string $property, $column = null): self
    {
        return new self($property, new FiltersScope(), $column);
    }

    /**
     * Specify a custom filter that will execute when the filter is requested.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function custom(string $property, Filter $filterClass, $column = null): self
    {
        return new self($property, $filterClass, $column);
    }

    /**
     * Specify a callable that will execute when the filter is requested.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function callback(string $property, callable $callback, $column = null): self
    {
        return new self($property, new FiltersCallback($callback), $column);
    }

    /**
     * The column(or property if column is null) between the input of property.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function between(string $property, $column = null): self
    {
        return new self($property, new FiltersBetween(), $column);
    }

    /**
     * The column(or property if column is null) between the datetime of the input of property.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function betweenDateTime(string $property, $column = null): self
    {
        return new self($property, new FiltersBetweenDateTime(), $column);
    }

    /**
     * The column(or property if column is null) between the date of the input of property.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function betweenDate(string $property, $column = null): self
    {
        return new self($property, new FiltersBetweenDate(), $column);
    }

    /**
     * The column(or property if column is null) equals the date of the input of property.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function date(string $property, $column = null): self
    {
        return new self($property, new FiltersDate(), $column);
    }
}
