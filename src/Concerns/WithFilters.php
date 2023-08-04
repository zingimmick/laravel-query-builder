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
    public function enableFilters(array|Filter|string $filters)
    {
        $filters = \is_array($filters) ? $filters : \func_get_args();
        $this->applyFilters($this->formatFilters($filters));

        return $this;
    }

    /**
     * @param array<(string|\Zing\QueryBuilder\Filter)> $filters
     */
    protected function formatFilters(array $filters): Collection
    {
        return collect($filters)->map(
            static function ($filter): Filter {
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
                    $filter->filter($this->getBuilder(), $this->getFilterValue($filter));

                    return;
                }

                if ($filter->hasDefault()) {
                    $filter->filter($this->getBuilder(), $filter->getDefault());
                }
            }
        );
    }

    protected function isRequestedFilter(Filter $filter): bool
    {
        return $this->request->has($filter->getProperty());
    }

    protected function getFilterValue(Filter $filter): mixed
    {
        return $this->request->input($filter->getProperty());
    }
}
