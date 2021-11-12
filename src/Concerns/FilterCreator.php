<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Contracts\Filter;
use Zing\QueryBuilder\Filters\BetweenDateFilter;
use Zing\QueryBuilder\Filters\BetweenDateTimeFilter;
use Zing\QueryBuilder\Filters\BetweenFilter;
use Zing\QueryBuilder\Filters\CallbackFilter;
use Zing\QueryBuilder\Filters\DateFilter;
use Zing\QueryBuilder\Filters\ExactFilter;
use Zing\QueryBuilder\Filters\PartialFilter;
use Zing\QueryBuilder\Filters\ScopeFilter;
use Zing\QueryBuilder\QueryConfiguration;

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
        $this->delimiter = QueryConfiguration::getDelimiter();
    }

    /**
     * The input of property equals the column(or property if column is null).
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function exact(string $property, $column = null): self
    {
        return new self($property, new ExactFilter(), $column);
    }

    /**
     * The input of property in the column(or property if column is null).
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function partial(string $property, $column = null): self
    {
        return new self($property, new PartialFilter(), $column);
    }

    /**
     * Specify a scope(property if column is null) that will execute when the filter is requested.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function scope(string $property, $column = null): self
    {
        return new self($property, new ScopeFilter(), $column);
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
        return new self($property, new CallbackFilter($callback), $column);
    }

    /**
     * The column(or property if column is null) between the input of property.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function between(string $property, $column = null): self
    {
        return new self($property, new BetweenFilter(), $column);
    }

    /**
     * The column(or property if column is null) between the datetime of the input of property.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function betweenDateTime(string $property, $column = null): self
    {
        return new self($property, new BetweenDateTimeFilter(), $column);
    }

    /**
     * The column(or property if column is null) between the date of the input of property.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function betweenDate(string $property, $column = null): self
    {
        return new self($property, new BetweenDateFilter(), $column);
    }

    /**
     * The column(or property if column is null) equals the date of the input of property.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function date(string $property, $column = null): self
    {
        return new self($property, new DateFilter(), $column);
    }

    /**
     * Specify a callable that will execute when the filter is requested or default when filter is requested not.
     */
    public static function boolean(string $property, callable $callback, ?callable $default = null): self
    {
        return self::callback($property, function (Builder $query, $value, $property) use ($callback, $default) {
            return $query->when($value, $callback, $default);
        });
    }
}
