<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Concerns\FilterCreator;
use Zing\QueryBuilder\Enums\CastType;

class Filter
{
    use FilterCreator;

    /**
     * @param mixed $value
     */
    public function filter(Builder $query, $value): Builder
    {
        $value = $this->resolveValueForFiltering($value);
        if ($value === null) {
            return $query;
        }

        if ($value === '') {
            return $query;
        }

        return $this->filter->apply($query, $value, $this->column);
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
     * @return \Illuminate\Database\Query\Expression|string
     */
    public function getColumn()
    {
        return $this->column;
    }

    public function withCast(string $cast): self
    {
        $this->cast = $cast;

        return $this;
    }

    public function hasCast(): bool
    {
        return $this->cast !== null;
    }

    public function getCast(): ?string
    {
        return $this->cast;
    }

    /**
     * @param mixed[] $values
     */
    public function ignore(array $values): self
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

    /**
     * @param mixed $value
     */
    public function default($value): self
    {
        $this->default = $value;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    public function hasDefault(): bool
    {
        return $this->default !== null;
    }

    /**
     * @param mixed $value
     *
     * @return false|mixed|string[]
     */
    protected function castValue($value)
    {
        $cast = $this->getCast();
        if ($cast === CastType::STRING) {
            return $value;
        }

        if ($cast === CastType::BOOLEAN) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($cast === CastType::INTEGER) {
            return filter_var($value, FILTER_VALIDATE_INT);
        }

        if ($cast === CastType::ARRAY) {
            return explode($this->delimiter, $value);
        }

        if (in_array(strtolower($value), ['true', 'false'], true)) {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        if (Str::contains($value, $this->delimiter)) {
            $value = explode($this->delimiter, $value);
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return mixed[]|mixed|null
     */
    protected function resolveValueForFiltering($value)
    {
        if (is_string($value)) {
            $value = $this->castValue($value);
        }

        if (is_array($value)) {
            $remainingProperties = array_diff($value, $this->getIgnored()->toArray());

            return empty($remainingProperties) ? null : $remainingProperties;
        }

        return $this->getIgnored()
            ->contains($value) ? null : $value;
    }

    /**
     * @phpstan-var non-empty-string
     * @var string
     */
    private $delimiter = ',';

    /**
     * @param non-empty-string $delimiter
     * @return $this
     */
    public function delimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }
}
