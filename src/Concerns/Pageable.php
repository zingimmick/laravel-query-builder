<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Zing\QueryBuilder\Paginator;

trait Pageable
{
    /**
     * @param string|\Zing\QueryBuilder\Paginator $paginator
     *
     * @return $this
     */
    public function enablePaginator($paginator = null)
    {
        $paginator = $paginator instanceof Paginator ? $paginator : Paginator::name($paginator);
        $perPage = $this->request->input($paginator->getName(), $paginator->getDefault());
        $this->builder->getModel()->setPerPage($perPage);

        return $this;
    }
}
