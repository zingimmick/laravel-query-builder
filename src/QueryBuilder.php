<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\ForwardsCalls;
use Zing\QueryBuilder\Concerns\Pageable;
use Zing\QueryBuilder\Concerns\WithFilters;
use Zing\QueryBuilder\Concerns\WithFlaggedFilter;
use Zing\QueryBuilder\Concerns\WithSearchable;
use Zing\QueryBuilder\Concerns\WithSorts;
use Zing\QueryBuilder\Concerns\WithTypedFilter;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class QueryBuilder
{
    use ForwardsCalls;
    use Pageable;
    use WithFilters;
    use WithFlaggedFilter;
    use WithSearchable;
    use WithSorts;
    use WithTypedFilter;

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation $builder
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(
        protected $builder,
        protected $request
    ) {
    }

    public static function fromBuilder(Builder|Relation|string $baseQuery, Request $request): self
    {
        if (is_subclass_of($baseQuery, Model::class)) {
            $baseQuery = forward_static_call([$baseQuery, 'query']);
        }

        return new self($baseQuery, $request);
    }

    public function getBuilder(): Builder
    {
        if ($this->builder instanceof Relation) {
            return $this->builder->getQuery();
        }

        return $this->builder;
    }

    /**
     * @param string $name
     */
    public function __get($name): mixed
    {
        return $this->builder->{$name};
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

        return $result === $this->builder ? $this : $result;
    }

    public function clone(): self
    {
        return clone $this;
    }

    public function __clone()
    {
        $this->builder = clone $this->builder;
    }
}
