<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Samples;

class SampleCollector
{
    /**
     * @return \Zing\QueryBuilder\Samples\Sample[]
     */
    public function samples(): array
    {
        return [
            new Sample('Basic usage', '', [new CodeSample(
                <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;

QueryBuilder::fromBuilder(User::class, $request)
    ->searchable(['name'])
    ->enableFilters(['is_visible', 'status'])
    ->enableSorts(['created_at'])
    ->enablePaginator()
    ->simplePaginate();
CODE_SAMPLE
                ,
                [
                    new IOSample(
                        '/api/users?search=Harry&status=1,2,3&desc=created_at&per_page=10',
                        'select * from "users" where ("name" like "%Harry%") and "status" in ("1", "2", "3") order by "created_at" desc limit 11 offset 0'
                    ),
                ]
            ),
            ]),

            new Sample(
                'Search',
                '',
                [new CodeSample(
                    <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;

QueryBuilder::fromBuilder(User::class, $request)
    ->searchable(['name', 'email'])
    ->simplePaginate();
CODE_SAMPLE
                    ,
                    [
                        new IOSample(
                            '/api/users?search=Harry',
                            'select * from "users" where ("name" like "%Harry%" or "email" like "%Harry%") limit 16 offset 0'
                        ),
                    ]
                ),
                ]
            ),

            (new Sample(
                'Search',
                'Composite search',
                [new CodeSample(
                    <<<'CODE_SAMPLE'
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;

QueryBuilder::fromBuilder(User::class, $request)
    ->searchable(['number', Filter::exact('encoded_id', 'id')])
    ->simplePaginate();
CODE_SAMPLE
                    ,
                    [
                        new IOSample(
                            '/api/users?search=2021',
                            'select * from "users" where ("number" like "%2021%" or ("id" = "2021")) limit 16 offset 0'
                        ),
                    ]
                ),
                ]
            ))->description('⚠️ The filter with default value is not supported yet.'),
            new Sample(
                'Filter',
                '',
                [
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;
use Zing\QueryBuilder\Filter;

QueryBuilder::fromBuilder(User::class, $request)
    ->enableFilters([
        Filter::partial('name')
    ])
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [
                            new IOSample(
                                '/api/users?name=Harry',
                                'select * from "users" where "name" like "%Harry%" limit 16 offset 0'
                            ),
                        ]
                    ),
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;
use Zing\QueryBuilder\Filter;

QueryBuilder::fromBuilder(User::class, $request)
    ->enableFilters([
        Filter::exact('status')
    ])
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [
                            new IOSample(
                                '/api/users?status=1,2,3',
                                'select * from "users" where "status" in ("1", "2", "3") limit 16 offset 0'
                            ),
                        ]
                    ),
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;
use Zing\QueryBuilder\Filter;

QueryBuilder::fromBuilder(User::class, $request)
    ->enableFilters([
        Filter::scope('visible')->default(true)
    ])
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [
                            new IOSample(
                                '/api/users?visible=1',
                                'select * from "users" where "is_visible" = true limit 16 offset 0'
                            ),
                            new IOSample(
                                '/api/users',
                                'select * from "users" where "is_visible" = true limit 16 offset 0'
                            ),
                        ]
                    ),
                ]
            ),
            (new Sample(
                'Filter',
                'Typed filter',
                [
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

QueryBuilder::fromBuilder(Order::class, $request)
    ->enableTypedFilter('search_type', 'search_value', [Filter::partial('number'), Filter::partial('user_name', 'user.name')])
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [
                            new IOSample(
                                '/api/users?search_type=number&search_value=2021',
                                'select * from "orders" where "number" like "%2021%" limit 16 offset 0'
                            ),
                        ]
                    ),
                ]
            ))->description('⚠️ The filter with default value is not supported yet.'),
            (new Sample(
                'Filter',
                'Flagged filter',
                [
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

QueryBuilder::fromBuilder(Order::class, $request)
    ->enableFlaggedFilter([Filter::partial('number'), Filter::partial('user_name', 'user.name')])
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [
                            new IOSample(
                                '/api/users?number=2021&user_name=Jone',
                                'select * from "orders" where (("number" like "%2021%") or (exists (select * from "users" where "orders"."user_id" = "users"."id" and "users"."name" like "%Jone%"))) limit 16 offset 0'
                            ),
                        ]
                    ),
                ]
            )),
            new Sample(
                'Filter',
                'Cast Input(Skip auto cast)',
                [
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\Enums\CastType;

QueryBuilder::fromBuilder(User::class, $request)
    ->enableFilters(Filter::exact('is_visible')->withCast(CastType::BOOLEAN))
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [
                            new IOSample(
                                '/api/users?is_visible=true',
                                'select * from "users" where "is_visible" = true limit 16 offset 0'
                            ),
                        ]
                    ),
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Tests\Models\Order;

QueryBuilder::fromBuilder(Order::class, $request)
    ->enableFilters(Filter::partial('content')->withCast(CastType::STRING))
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [
                            new IOSample(
                                '/api/orders?content=code,and',
                                'select * from "orders" where "content" like "%code,and%" limit 16 offset 0'
                            ),
                        ]
                    ),
                ]
            ),
            new Sample(
                'Sort',
                '',
                [
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Illuminate\Support\Facades\DB;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Sort;
use Zing\QueryBuilder\Tests\Models\Order;

QueryBuilder::fromBuilder(Order::class, $request)
    ->enableSorts([Sort::field('created_date', 'created_at')])
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [
                            new IOSample(
                                '/api/users?desc=created_date',
                                'select * from "orders" order by "created_at" desc limit 16 offset 0'
                            ),
                        ]
                    ),
                ]
            ),
            new Sample(
                'Paginator',
                '',
                [
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;
use Zing\QueryBuilder\Paginator;

QueryBuilder::fromBuilder(User::class, $request)
    ->enablePaginator('size')
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [new IOSample('/api/users?size=5', 'select * from "users" limit 6 offset 0')]
                    ),
                    new CodeSample(
                        <<<'CODE_SAMPLE'
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;
use Zing\QueryBuilder\Paginator;

QueryBuilder::fromBuilder(User::class, $request)
    ->enablePaginator(Paginator::name('size')->default(5))
    ->simplePaginate();
CODE_SAMPLE
                        ,
                        [new IOSample('/api/users?size=', 'select * from "users" limit 6 offset 0')]
                    ),
                ]
            ),
        ];
    }
}
