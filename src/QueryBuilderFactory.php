<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Zing\QueryBuilder\Builders\QueryBuilder;

class QueryBuilderFactory
{
    public static function create($baseQuery, Request $request)
    {
        if (is_subclass_of($baseQuery, Model::class)) {
            $baseQuery = forward_static_call([$baseQuery, 'query']);
        }

        return new QueryBuilder($baseQuery, $request);
    }
}
