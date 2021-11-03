<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Support\Collection;
use Zing\QueryBuilder\Filter;

trait WithFilters
{
    /**
     * @param array<(string|\Zing\QueryBuilder\Filter)>|string|\Zing\QueryBuilder\Filter $filters
     *
     * @return $this
     */
    public function enableFilters($filters)
    {
        $filters = is_array($filters) ? $filters : func_get_args();
        $this->applyFilters($this->formatFilters($filters));

        return $this;
    }

    /**
     * @param array<(string|\Zing\QueryBuilder\Filter)> $filters
     *
     * @return \Illuminate\Support\Collection
     */
    protected function formatFilters(array $filters)
    {
        return collect($filters)->map(
            function ($filter): Filter {
                if ($filter instanceof Filter) {
                    return $filter;
                }

                return Filter::exact($filter);
            }
        );
    }

    protected function applyFilters(Collection $filters): void
    {
        $filters->each(
            function (Filter $filter): void {
                $thisIsRequestedFilter = $this->isRequestedFilter($filter);
                if ($thisIsRequestedFilter) {
                    $filter->filter($this->builder, $this->getFilterValue($filter));

                    return;
                }

                if ($filter->hasDefault()) {
                    $filter->filter($this->builder, $filter->getDefault());
                }
            }
        );
    }

    /**
     * @return bool
     */
    protected function isRequestedFilter(Filter $filter)
    {
        return $this->request->has($filter->getProperty());
    }

    /**
     * @return mixed
     */
    protected function getFilterValue(Filter $filter)
    {
        return $this->request->input($filter->getProperty());
    }
}
