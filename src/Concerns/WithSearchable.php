<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait WithSearchable
{
    use NestedRelation;

    /**
     * @param string|array<string> $searchable
     *
     * @return $this
     */
    public function searchable($searchable)
    {
        $searchable = is_array($searchable) ? $searchable : func_get_args();
        $search = $this->request->input('search');
        if ($search === null) {
            return $this;
        }

        if (is_string($search) && trim($search) === '') {
            return $this;
        }

        $searchable = $this->resolveNestedSearchable($searchable);

        return $this->applySearchable($search, $searchable);
    }

    /**
     * @param mixed $search
     * @param array<array<string>|string> $searchable
     *
     * @return $this
     */
    protected function applySearchable($search, array $searchable = [])
    {
        $this->where(
            function (Builder $query) use ($search, $searchable): void {
                collect($searchable)->each(
                    function ($value, $key) use ($query, $search) {
                        if (is_numeric($key)) {
                            return $query->orWhere($value, 'like', sprintf('%%%s%%', $search));
                        }

                        return $this->applyRelationSearchable($query, $key, (array) $value, $search);
                    }
                );
            }
        );

        return $this;
    }

    /**
     * @param array<string> $fields
     * @param mixed $search
     */
    protected function applyRelationSearchable(Builder $query, string $relation, array $fields, $search): Builder
    {
        return $query->orWhereHas(
            $relation,
            function (Builder $query) use ($fields, $search): void {
                $query->where(
                    function (Builder $query) use ($fields, $search): void {
                        foreach ($fields as $field) {
                            $query->orWhere($field, 'like', sprintf('%%%s%%', $search));
                        }
                    }
                );
            }
        );
    }

    /**
     * @param array<string> $searchable
     *
     * @return array<array<string>|string>
     */
    private function resolveNestedSearchable(array $searchable)
    {
        $results = [];
        foreach ($searchable as $singleSearchable) {
            if (Str::contains($singleSearchable, '.')) {
                [$relation, $property] = $this->resolveNestedRelation($singleSearchable);

                $results[$relation][] = $property;
            } else {
                $results[] = $singleSearchable;
            }
        }

        return $results;
    }
}
