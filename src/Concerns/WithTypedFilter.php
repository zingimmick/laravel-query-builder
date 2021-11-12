<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Zing\QueryBuilder\Exceptions\ParameterException;
use Zing\QueryBuilder\Filter;

trait WithTypedFilter
{
    /**
     * @param array<string|\Zing\QueryBuilder\Filter> $filters
     *
     * @return $this
     */
    public function enableTypedFilter(string $type, string $value, array $filters): self
    {
        if (! $this->request->has($type)) {
            return $this;
        }

        $property = $this->request->input($type);
        $filterValue = $this->request->input($value);
        $filter = collect($filters)
            ->filter(function ($filter) use ($property): bool {
                $filter = $filter instanceof Filter ? $filter : Filter::exact($filter);

                if ($filter->getDefault() !== null) {
                    throw ParameterException::unsupportedFilterWithDefaultValueForTypedFilter();
                }

                return $filter->isForProperty($property);
            })
            ->first();
        if (! $filter instanceof \Zing\QueryBuilder\Filter) {
            return $this;
        }

        $filter->filter($this->builder, $filterValue);

        return $this;
    }
}
