<?php

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
        foreach ($filters as $filter) {
            if (! $filter instanceof Filter) {
                $filter = Filter::exact($filter);
            }
            if ($filter->getDefault() !== null) {
                throw ParameterException::unsupportedFilterWithDefaultValueForTypedFilter();
            }
            if ($filter->isForProperty($this->request->input($type))) {
                $filter->filter($this->builder, $this->request->input($value));
                return $this;
            }
        }
        return $this;
    }
}
