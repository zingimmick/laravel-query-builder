<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: zing
 * Date: 2018/12/26
 * Time: 7:14 PM.
 */

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Zing\QueryBuilder\Concerns\WithFilters;
use Zing\QueryBuilder\Concerns\WithSearchable;
use Zing\QueryBuilder\Concerns\WithSorts;

class QueryBuilder extends Builder
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
        $this->setModel($builder->getModel())->setEagerLoads($builder->getEagerLoads());
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
    public static function fromBuilder($baseQuery, Request $request)
    {
        if (is_subclass_of($baseQuery, Model::class)) {
            $baseQuery = forward_static_call([$baseQuery, 'query']);
        }

        return new static($baseQuery, $request);
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $perPage = $perPage ?: $this->request->input(config('query-builder.per_page.key', 'per_page'), config('query-builder.per_page.value', $this->model->getPerPage()));

        return parent::paginate($perPage, $columns, $pageName, $page);
    }
}
