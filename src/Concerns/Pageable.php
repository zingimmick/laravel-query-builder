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
    public function enablePaginator(string $paginator)
    {
        $paginator = is_string($paginator) ? Paginator::name($paginator) : $paginator;
        $this->builder->getModel()
            ->setPerPage($this->request->input($paginator->getName(), $paginator->getDefault()));

        return $this;
    }
}
