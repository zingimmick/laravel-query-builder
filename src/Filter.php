<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Concerns\FilterCreator;
use Zing\QueryBuilder\Enums\CastType;

class Filter
{
    use FilterCreator;

    public function filter($query, $value)
    {
        $value = $this->resolveValueForFiltering($value);
        if ($value === null || $value === '') {
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
