<?php
/**
 * Created by PhpStorm.
 * User: zing
 * Date: 2018/12/26
 * Time: 7:14 PM.
 */

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Zing\QueryBuilder\Concerns\Castable;

class QueryBuilder extends Builder
{
    use Castable;

    public const CAST_INTEGER = 'integer';

    public const CAST_ARRAY = 'array';

    public const CAST_BOOLEAN = 'boolean';

    /** @var \Illuminate\Http\Request */
    protected $request;

    public function __construct(Builder $builder, $request)
    {
        parent::__construct($builder->getQuery());
        $this->setModel($builder->getModel());
        $this->scopes = $builder->scopes;
        $this->request = $request;
    }

    /**
     * @param Builder|string $baseQuery
     * @param \Illuminate\Http\Request $request
     *
     * @return \Zing\QueryBuilder\QueryBuilder
     */
    public static function for($baseQuery, Request $request)
    {
        if (is_string($baseQuery)) {
            $baseQuery = $baseQuery::query();
        }

        return new static($baseQuery, $request);
    }

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
                data_get($inputs, $direction),
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

    protected function castAttribute($key, $value)
    {
        switch ($this->getCastType($key)) {
            case self::CAST_INTEGER:
                return (int) $value;
            case self::CAST_ARRAY:
                if (Str::contains($value, ',')) {
                    return explode(',', $value);
                }

                return $value;
            case self::CAST_BOOLEAN:
                return (bool) $value;
        }

        return $value;
    }

    /**
     * 自定义过滤器逻辑.
     *
     * @param \Zing\QueryBuilder\Filter[]|mixed $filters
     *
     * @return $this
     */
    public function addFilters($filters): self
    {
        $filters = is_array($filters) ? $filters : func_get_args();
        $filters = collect($filters)->map(function ($filter) {
            if ($filter instanceof Filter) {
                return $filter;
            }

            return Filter::exact($filter);
        });
        $filters->each(function (Filter $filter) {
            $value = data_get($this->request->input(), $filter->getProperty());
            if ($value !== null && $value !== '') {
                if ($this->hasCast($filter->getProperty())) {
                    $value = $this->castAttribute($filter->getProperty(), $value);
                } elseif (Str::contains($value, ',')) {
                    $value = explode(',', $value);
                }

                $filter->filter($this, $value);
            }
        });

        return $this;
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
