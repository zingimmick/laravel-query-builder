<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Zing\QueryBuilder\Sort;

trait WithSorts
{
    /**
     * 排序逻辑.
     *
     * @param array $sorts
     *
     * @return mixed
     */
    public function enableSorts($sorts)
    {
        $this->formatSorts($sorts)->each(
            function (Sort $sort): void {
                if ($this->isRequestedSort($sort)) {
                    $sort->sort($this, $this->getSortValue($sort));

                    return;
                }

                if ($sort->hasDefaultDirection()) {
                    $sort->sort($this, $sort->getDefaultDirection());

                    return;
                }
            }
        );

        return $this;
    }

    protected function isRequestedSort(Sort $sort)
    {
        return $this->request->input('asc') === $sort->getProperty() || $this->request->input('desc') === $sort->getProperty();
    }

    protected function getSortValue(Sort $sort)
    {
        if ($this->request->input('desc') === $sort->getProperty()) {
            return 'desc';
        }

        return 'asc';
    }

    public function formatSorts($sorts)
    {
        return collect($sorts)->map(
            function ($sort, $key) {
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
