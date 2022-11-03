<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Zing\QueryBuilder\Paginator;

trait Pageable
{
    /**
     * @return $this
     */
    public function enablePaginator(string|Paginator|null $paginator = null)
    {
        $paginator = $paginator instanceof Paginator ? $paginator : Paginator::name($paginator);
        $perPage = $this->request->input($paginator->getName()) ?: $paginator->getDefault();
        $this->getBuilder()
            ->getModel()
            ->setPerPage($perPage);

        return $this;
    }
}
