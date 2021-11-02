<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class QueryBuilderFactory
{
    /**
     * @var array<class-string, class-string<\Zing\QueryBuilder\QueryBuilder>>
     */
    protected $builders = [];

    /**
     * QueryBuilderFactory constructor.
     *
     * @param array<class-string, class-string<\Zing\QueryBuilder\QueryBuilder>> $builders
     */
    public function __construct(array $builders)
    {
        $this->builders = $builders;
    }

    /**
     * @param class-string<Model>|\Illuminate\Database\Eloquent\Builder $baseQuery
     *
     * @return \Zing\QueryBuilder\QueryBuilder
     */
    public function create($baseQuery, Request $request)
    {
        if (is_subclass_of($baseQuery, Model::class)) {
            $baseQuery = forward_static_call([$baseQuery, 'query']);
        }

        $class = get_class($baseQuery);

        if (isset($this->builders[$class])) {
            return $this->resolveBuilder($this->builders[$class], $baseQuery, $request);
        }

        foreach ($this->builders as $expected => $builder) {
            if (is_subclass_of($class, $expected)) {
                return $this->resolveBuilder($builder, $baseQuery, $request);
            }
        }

        return new QueryBuilder($baseQuery, $request);
    }

    /**
     * @param class-string<\Zing\QueryBuilder\QueryBuilder> $builder
     * @param class-string<Model>|\Illuminate\Database\Eloquent\Builder|bool $baseQuery
     * @param Request $request
     * @return \Zing\QueryBuilder\QueryBuilder
     */
    public function resolveBuilder($builder, $baseQuery, $request)
    {
        return new $builder($baseQuery, $request);
    }

    /**
     * @param mixed $builder
     * @param mixed $queryBuilder
     *
     * @return string[]|mixed[]
     */
    public function queryBy($builder, $queryBuilder): array
    {
        $this->builders[$builder] = $queryBuilder;

        return $this->builders;
    }
}
