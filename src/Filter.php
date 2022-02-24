<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
        $this->ignored = ($this->ignored === null ? collect($values) : $this->ignored
            ->merge($values))
            ->flatten();

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection<int, mixed>
     */
    public function getIgnored(): Collection
    {
        return $this->ignored ?: collect();
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
        if (! \is_string($value)) {
            return $value;
        }

        if ($cast === CastType::STRING) {
            return $value;
        }

        return collect(explode($this->delimiter, $value))
            ->map(function ($value) use ($cast) {
                if ($cast === CastType::STRING) {
                    return (string) $value;
                }
                if ($cast === CastType::INTEGER) {
                    return (int) $value;
                }
                if ($cast === CastType::BOOLEAN) {
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                if (\in_array(strtolower($value), ['true', 'false'], true)) {
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                return $value;
            })
            ->whenNotEmpty(function (Collection $collection) {
                return $collection->count() === 1 ? $collection->first() : $collection->all();
            });
    }

    /**
     * @param mixed $value
     *
     * @return mixed[]|mixed|null
     */
    protected function resolveValueForFiltering($value)
    {
        if (\is_string($value)) {
            $value = $this->castValue($value);
        }

        if (\is_array($value)) {
            $remainingProperties = array_diff($value, $this->getIgnored()->toArray());

            return empty($remainingProperties) ? null : $remainingProperties;
        }

        return $this->getIgnored()
            ->contains($value) ? null : $value;
    }

    /**
     * @param non-empty-string $delimiter
     */
    public function delimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }
}
