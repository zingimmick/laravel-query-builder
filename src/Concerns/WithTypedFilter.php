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

        $this->applyTypedFilters($type, $value, $filters);

        return $this;
    }

    /**
     * @param array<string|\Zing\QueryBuilder\Filter> $filters
     *
     */
    protected function applyTypedFilters(string $type, string $value, array $filters)
    {
        foreach ($filters as $filter) {
            $filter = $filter instanceof Filter ? $filter : Filter::exact($filter);

            if ($filter->getDefault() !== null) {
                throw ParameterException::unsupportedFilterWithDefaultValueForTypedFilter();
            }

            if (! $filter->isForProperty($this->request->input($type))) {
                continue;
            }

            $filter->filter($this->builder, $this->request->input($value));

            break;
        }
    }
}
