<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Zing\QueryBuilder\Filter;

trait WithFlaggedFilter
{
    /**
     * @param array<string|\Zing\QueryBuilder\Filter> $filters
     *
     * @return $this
     */
    public function enableFlaggedFilter(array $filters): self
    {
        $this->where(
            function (Builder $query) use ($filters): void {
                $this->formatFilters($filters)
                    ->each(
                        function (Filter $filter) use ($query): void {
                        $query->orWhere(function ($query) use ($filter): void {
                            $thisIsRequestedFilter = $this->isRequestedFilter($filter);
                            if ($thisIsRequestedFilter) {
                                $filter->filter($query, $this->getFilterValue($filter));

                                return;
                            }

                            if ($filter->hasDefault()) {
                                $filter->filter($query, $filter->getDefault());
                            }
                        });
                    }
                    );
            }
        );

        return $this;
    }
}
