<?php

namespace Zing\QueryBuilder\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait WithSearchable
{
    /**
     * @param string $field
     * @param array $results
     *
     * @return array
     */
    private function addNestedRelation($field, array $results)
    {
        [$relation, $property] = collect(explode('.', $field))
            ->pipe(function (Collection $parts) {
                return [
                    $parts->except(count($parts) - 1)->map([Str::class, 'camel'])->implode('.'),
                    $parts->last(),
                ];
            });

        $results[$relation][] = $property;

        return $results;
    }

    public function searchable($searchable)
    {
        $searchable = is_array($searchable) ? $searchable : func_get_args();
        $search = $this->request->input('search');
        if ($search) {
            $this->where(
                function (Builder $query) use ($search, $searchable) {
                    $results = [];
                    foreach ($searchable as $field) {
                        if (Str::contains($field, '.')) {
                            $results = $this->addNestedRelation($field, $results);
                        } else {
                            $results[] = $field;
                        }
                    }
                    foreach ($results as $key => $value) {
                        if (is_numeric($key)) {
                            $query->orWhere($value, 'like', "%{$search}%");
                        } else {
                            $query->orWhereHas(
                                $key,
                                function ($query) use ($value, $search) {
                                    /** @var \Illuminate\Database\Query\Builder $query */
                                    $query->where(
                                        function (Builder $query) use ($value, $search) {
                                            foreach ($value as $field) {
                                                $query->orWhere($field, 'like', "%{$search}%");
                                            }
                                        }
                                    );
                                }
                            );
                        }
                    }
                }
            );
        }//end if
        return $this;
    }
}
