<?php

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait WithSorts
{
    /**
     * 排序逻辑.
     *
     * @param array $inputs
     * @param array $sorts
     *
     * @return mixed
     */
    protected function applySort($inputs, $sorts)
    {
        foreach (['desc', 'asc'] as $direction) {
            $this->when(
                $this->request->input($direction),
                function (Builder $query, $descAttribute) use ($sorts, $direction) {
                    if (array_key_exists($descAttribute, $sorts)) {
                        $descAttribute = Arr::get($sorts, $descAttribute);
                    }

                    return $query->orderBy($descAttribute, $direction);
                }
            );
        }

        return $this;
    }
}
