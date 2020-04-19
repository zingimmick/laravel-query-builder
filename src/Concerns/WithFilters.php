<?php

namespace Zing\QueryBuilder\Concerns;

use Zing\QueryBuilder\Filter;

trait WithFilters
{
    protected $filters;

    public function enableFilters($filters)
    {
        $filters = is_array($filters) ? $filters : func_get_args();
        $this->filters = $this->formatFilters($filters);
        $this->applyFilters();

        return $this;
    }

    /**
     * 自定义过滤器逻辑.
     *
     * @param \Zing\QueryBuilder\Filter[]|mixed $filters
     *
     * @return $this
     *
     * @deprecated use enableFilters instead
     */
    public function addFilters($filters): self
    {
        return $this->enableFilters(is_array($filters) ? $filters : func_get_args());
    }

    protected function formatFilters($filters)
    {
        return collect($filters)->map(function ($filter) {
            if ($filter instanceof Filter) {
                return $filter;
            }

            return Filter::exact($filter);
        });
    }

    protected function applyFilters()
    {
        $this->filters->each(function (Filter $filter) {
            if ($this->isRequestedFilter($filter)) {
                $filter->filter($this, $this->getFilterValue($filter));

                return;
            }
            if ($filter->hasDefault()) {
                $filter->filter($this, $filter->getDefault());

                return;
            }
        });
    }

    protected function isRequestedFilter(Filter $filter)
    {
        return $this->request->has($filter->getProperty());
    }

    protected function getFilterValue(Filter $filter)
    {
        return $this->request->input($filter->getProperty());
    }
}
