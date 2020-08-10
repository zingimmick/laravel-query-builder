<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Filters\FiltersBetween;
use Zing\QueryBuilder\Filters\FiltersBetweenDate;
use Zing\QueryBuilder\Filters\FiltersBetweenDateTime;
use Zing\QueryBuilder\Filters\FiltersCallback;
use Zing\QueryBuilder\Filters\FiltersExact;
use Zing\QueryBuilder\Filters\FiltersPartial;
use Zing\QueryBuilder\Filters\FiltersScope;

class Filter
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

    public function filter($query, $value)
    {
        $value = $this->resolveValueForFiltering($value);
        if ($value === null || $value === '') {
            return $query;
        }

        return $this->filter->apply($query, $value, $this->column);
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

    public static function callback(string $property, $callback, $column = null): self
    {
        return new static($property, new FiltersCallback($callback), $column);
    }

    public static function between(string $property, $column = null): self
    {
        return new static($property, new FiltersBetween(), $column);
    }

    public static function betweenDateTime(string $property, $column = null): self
    {
        return new static($property, new FiltersBetweenDateTime(), $column);
    }

    public static function betweenDate(string $property, $column = null): self
    {
        return new static($property, new FiltersBetweenDate(), $column);
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

    public function withCast($cast)
    {
        $this->cast = $cast;

        return $this;
    }

    public function hasCast(): bool
    {
        return isset($this->cast);
    }

    public function getCast()
    {
        return $this->cast;
    }

    public function ignore(...$values): self
    {
        $this->ignored = $this->ignored
            ->merge($values)
            ->flatten();

        return $this;
    }

    public function getIgnored(): Collection
    {
        return $this->ignored;
    }

    public function default($value): self
    {
        $this->default = $value;

        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function hasDefault(): bool
    {
        return isset($this->default);
    }

    protected function castValue($value)
    {
        switch ($this->getCast()) {
            case CastType::CAST_BOOLEAN:
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case CastType::CAST_INTEGER:
                return filter_var($value, FILTER_VALIDATE_INT);
            case CastType::CAST_ARRAY:
                return explode(',', $value);
            default:
                if (in_array(strtolower($value), ['true', 'false'], true)) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                if (Str::contains($value, ',')) {
                    $value = explode(',', $value);
                }

                return $value;
        }//end switch
    }

    protected function resolveValueForFiltering($value)
    {
        if (is_string($value)) {
            $value = $this->castValue($value);
        }

        if (is_array($value)) {
            $remainingProperties = array_diff($value, $this->getIgnored()->toArray());

            return ! empty($remainingProperties) ? $remainingProperties : null;
        }

        return ! $this->getIgnored()->contains($value) ? $value : null;
    }
}
