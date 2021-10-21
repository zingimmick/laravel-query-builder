<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class QueryBuilderFactory
{
    /**
     * @var string[]|mixed[]
     */
    protected $builders = [];

    /**
     * QueryBuilderFactory constructor.
     *
     * @param string[] $builders
     */
    public function __construct(array $builders)
    {
        $this->builders = $builders;
    }

    /**
     * @param mixed $baseQuery
     *
     * @return mixed|\Zing\QueryBuilder\QueryBuilder
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
