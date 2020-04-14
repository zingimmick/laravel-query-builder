<?php

namespace Zing\QueryBuilder\Concerns;

use Zing\QueryBuilder\Filter;

trait FiltersQuery
{
    /** @var \Illuminate\Support\Collection */
    protected $allowedFilters;

    public function allowedFilters($filters): self
    {
        $filters = is_array($filters) ? $filters : func_get_args();

        $this->allowedFilters = collect($filters)->map(function ($filter) {
            if ($filter instanceof Filter) {
                return $filter;
            }

            return Filter::partial($filter);
        });

        $this->ensureAllFiltersExist();

        $this->addFiltersToQuery();

        return $this;
    }

    protected function addFiltersToQuery()
    {
        $this->allowedFilters->each(function (Filter $filter) {
            if ($this->isFilterRequested($filter)) {
                $value = $this->request->filters()->get($filter->getName());
                $filter->filter($this, $value);

                return;
            }

            if ($filter->hasDefault()) {
                $filter->filter($this, $filter->getDefault());

                return;
            }
        });
    }

    protected function findFilter(string $property): ?Filter
    {
        return $this->allowedFilters
            ->first(function (Filter $filter) use ($property) {
                return $filter->isForFilter($property);
            });
    }

    protected function isFilterRequested(Filter $allowedFilter): bool
    {
        return $this->request->filters()->has($allowedFilter->getName());
    }

    public function filtersRequested()
    {
    }

    protected function ensureAllFiltersExist()
    {
        $filterNames = $this->request->filters()->keys();

        $allowedFilterNames = $this->allowedFilters->map(function (Filter $allowedFilter) {
            return $allowedFilter->getName();
        });

        $diff = $filterNames->diff($allowedFilterNames);

        if ($diff->count()) {
            throw InvalidFilterQuery::filtersNotAllowed($diff, $allowedFilterNames);
        }
    }
}
