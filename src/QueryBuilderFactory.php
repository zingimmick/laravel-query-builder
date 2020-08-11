<?php

declare(strict_types=1);

namespace Zing\QueryBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Helpers\EloquentBuilder;
use Zing\QueryBuilder\Builders\HybridQueryBuilder;
use Zing\QueryBuilder\Builders\MongodbQueryBuilder;
use Zing\QueryBuilder\Builders\QueryBuilder;

class QueryBuilderFactory
{
    public static function create($baseQuery, Request $request)
    {
        if (is_subclass_of($baseQuery, Model::class)) {
            $baseQuery = forward_static_call([$baseQuery, 'query']);
        }

        if ($baseQuery instanceof Builder) {
            return new MongodbQueryBuilder($baseQuery, $request);
        }

        if ($baseQuery instanceof EloquentBuilder) {
            return new HybridQueryBuilder($baseQuery, $request);
        }

        return new QueryBuilder($baseQuery, $request);
    }
}
