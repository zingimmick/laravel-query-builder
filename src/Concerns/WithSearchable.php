<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait WithSearchable
{
    use NestedRelation;

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

    protected function applySearchable($search, array $searchable = [])
    {
        return $this->where(
            function (Builder $query) use ($search, $searchable): void {
                collect($searchable)->each(
                    function ($value, $key) use ($query, $search) {
                        if (is_numeric($key)) {
                            return $query->orWhere($value, 'like', "%{$search}%");
                        }

                        return $this->applyRelationSearchable($query, $key, $value, $search);
                    }
                );
            }
        );
    }

    protected function applyRelationSearchable($query, $relation, $fields, $search)
    {
        return $query->orWhereHas(
            $relation,
            function (Builder $query) use ($fields, $search): void {
                $query->where(
                    function (Builder $query) use ($fields, $search): void {
                        foreach ($fields as $field) {
                            $query->orWhere($field, 'like', "%{$search}%");
                        }
                    }
                );
            }
        );
    }

    /**
     * @param string $field
     * @param array $results
     *
     * @return array
     */
    private function addNestedRelation($field, array $results)
    {
        [$relation, $property] = $this->resolveNestedRelation($field);

        $results[$relation][] = $property;

        return $results;
    }

    private function resolveNestedSearchable(array $searchable)
    {
        $results = [];
        foreach ($searchable as $singleSearchable) {
            if (Str::contains($singleSearchable, '.')) {
                $results = $this->addNestedRelation($singleSearchable, $results);
            } else {
                $results[] = $singleSearchable;
            }
        }

        return $results;
    }
}
