<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait Queryable
{
    use WithFilters;
    use WithSearchable;
    use WithSorts;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Builder $builder, $request)
    {
        parent::__construct($builder->getQuery());

        $this->setModel($builder->getModel())
            ->setEagerLoads($builder->getEagerLoads());
        $this->scopes = $builder->scopes;
        $this->localMacros = $builder->localMacros;
        $this->onDelete = $builder->onDelete;
        $this->request = $request;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|string $baseQuery
     * @param \Illuminate\Http\Request $request
     *
     * @return \Zing\QueryBuilder\QueryBuilder
     */
    public static function fromBuilder($baseQuery, Request $request): self
    {
        if (is_subclass_of($baseQuery, Model::class)) {
            $baseQuery = forward_static_call([$baseQuery, 'query']);
        }

        return new self($baseQuery, $request);
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $perPage = $perPage ?: $this->resolvePerPage();

        return parent::paginate((int) $perPage, $columns, $pageName, $page);
    }

    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $perPage = $perPage ?: $this->resolvePerPage();

        return parent::simplePaginate((int) $perPage, $columns, $pageName, $page);
    }

    protected function resolvePerPage()
    {
        return $this->request->input(
            config('query-builder.per_page.key', 'per_page'),
            config('query-builder.per_page.value', $this->model->getPerPage())
        );
    }
}
