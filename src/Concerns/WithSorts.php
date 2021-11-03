<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Zing\QueryBuilder\Sort;

trait WithSorts
{
    /**
     * @param array<string|Sort> $sorts
     *
     * @return $this
     */
    public function enableSorts(array $sorts)
    {
        $this->formatSorts($sorts)
            ->each(
                function (Sort $sort): void {
                    $thisIsRequestedSort = $this->isRequestedSort($sort);
                    if ($thisIsRequestedSort) {
                        $sort->sort($this->builder, $this->getSortValue($sort));

                        return;
                    }

                    if ($sort->hasDefaultDirection()) {
                        $sort->sort($this->builder, $sort->getDefaultDirection());

                        return;
                    }
                }
            );

        return $this;
    }

    /**
     * @return bool
     */
    protected function isRequestedSort(Sort $sort)
    {
        if ($this->request->input('asc') === $sort->getProperty()) {
            return true;
        }

        return $this->request->input('desc') === $sort->getProperty();
    }

    /**
     * @return string
     */
    protected function getSortValue(Sort $sort)
    {
        if ($this->request->input('desc') === $sort->getProperty()) {
            return 'desc';
        }

        return 'asc';
    }

    /**
     * @param mixed $sorts
     *
     * @return \Illuminate\Support\Collection
     */
    public function formatSorts($sorts)
    {
        return collect($sorts)->map(
            function ($sort, $key): Sort {
                if ($sort instanceof Sort) {
                    return $sort;
                }

                if (is_string($key)) {
                    return Sort::field($key, $sort);
                }

                return Sort::field($sort);
            }
        );
    }
}
