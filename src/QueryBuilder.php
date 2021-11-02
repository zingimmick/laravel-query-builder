<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\ForwardsCalls;
use Zing\QueryBuilder\Concerns\Pageable;
use Zing\QueryBuilder\Concerns\WithFilters;
use Zing\QueryBuilder\Concerns\WithSearchable;
use Zing\QueryBuilder\Concerns\WithSorts;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class QueryBuilder
{
    use WithFilters;
    use WithSearchable;
    use WithSorts;
    use Pageable;
    use ForwardsCalls;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Builder $builder, $request)
    {
        $this->builder = $builder;
        $this->request = $request;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|string $baseQuery
     */
    public static function fromBuilder($baseQuery, Request $request): self
    {
        if (is_subclass_of($baseQuery, Model::class)) {
            $baseQuery = forward_static_call([$baseQuery, 'query']);
        }

        return new self($baseQuery, $request);
    }

    /**
     * @param string $name
     * @param mixed[] $arguments
     *
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        $result = $this->forwardCallTo($this->builder, $name, $arguments);

        /*
         * If the forwarded method call is part of a chain we can return $this
         * instead of the actual $result to keep the chain going.
         */
        if ($result === $this->builder) {
            return $this;
        }

        return $result;
    }
}
