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
        $filter = $this->formatFilters($filters)
            ->filter(function ($filter) use ($property): bool {
                if ($filter->getDefault() !== null) {
                    throw ParameterException::unsupportedFilterWithDefaultValueForTypedFilter();
                }

                return $filter->isForProperty($property);
            })
            ->first();
        if (! $filter instanceof Filter) {
            return $this;
        }

        $filter->filter($this->getBuilder(), $filterValue);

        return $this;
    }
}
