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
     * @var string
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

    protected $cast;

    public function __construct(string $property, $filter, $column = null)
    {
        $this->property = $property;

        $this->filter = $filter;
        $this->ignored = collect();
        $this->column = $column ?? $property;
    }

    /**
     * 通过另一个字段查询.
     *
     * @param \Illuminate\Database\Query\Expression|string|null $column
     */
    public static function exact(string $property, $column = null): self
    {
        return new self($property, new FiltersExact(), $column);
    }

    /**
     * 通过字段模糊查询.
     *
     * @param string|null $column
     */
    public static function partial(string $property, $column = null): self
    {
        return new self($property, new FiltersPartial(), $column);
    }

    /**
     * 通过作用域查询.
     *
     * @param string|null $column
     *
     * @return \Zing\QueryBuilder\Filter
     */
    public static function scope(string $property, $column = null): self
    {
        return new self($property, new FiltersScope(), $column);
    }

    /**
     * 自定义过滤器.
     *
     * @param string|null $column
     *
     * @return \Zing\QueryBuilder\Filter
     */
    public static function custom(string $property, Filter $filterClass, $column = null): self
    {
        return new self($property, $filterClass, $column);
    }

    public static function callback(string $property, $callback, $column = null): self
    {
        return new self($property, new FiltersCallback($callback), $column);
    }

    public static function between(string $property, $column = null): self
    {
        return new self($property, new FiltersBetween(), $column);
    }

    public static function betweenDateTime(string $property, $column = null): self
    {
        return new self($property, new FiltersBetweenDateTime(), $column);
    }

    public static function betweenDate(string $property, $column = null): self
    {
        return new self($property, new FiltersBetweenDate(), $column);
    }

    public static function date(string $property, $column = null): self
    {
        return new self($property, new FiltersDate(), $column);
    }
}
