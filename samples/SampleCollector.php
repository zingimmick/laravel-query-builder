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
                __DIR__ . '/basic/basic.php',
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
                    __DIR__ . '/search/search.php',
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
                    __DIR__ . '/search/composite_search.php',
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
                        __DIR__ . '/filter/filter_column_contains_property_value.php',
                        [
                            new IOSample(
                                '/api/users?name=Harry',
                                'select * from "users" where "name" like "%Harry%" limit 16 offset 0'
                            ),
                        ]
                    ),
                    new CodeSample(
                        __DIR__ . '/filter/filter_column_equals_to_property_value.php',
                        [
                            new IOSample(
                                '/api/users?status=1,2,3',
                                'select * from "users" where "status" in ("1", "2", "3") limit 16 offset 0'
                            ),
                        ]
                    ),
                    new CodeSample(
                        __DIR__ . '/filter/filter_with_scope_and_default.php',
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
                        __DIR__ . '/filter/filter_orders_with_type_and_value.php',
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
                        __DIR__ . '/filter/filter_orders_match_any_filters.php',
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
                        __DIR__ . '/filter/cast_value_to_boolean.php',
                        [
                            new IOSample(
                                '/api/users?is_visible=true',
                                'select * from "users" where "is_visible" = true limit 16 offset 0'
                            ),
                        ]
                    ),
                    new CodeSample(
                        __DIR__ . '/filter/cast_value_force_to_string.php',
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
                    new CodeSample(__DIR__ . '/sort/sort_users_by_created_date.php', [
                        new IOSample(
                            '/api/users?desc=created_date',
                            'select * from "orders" order by "created_at" desc limit 16 offset 0'
                        ),
                    ]),
                ]
            ),
            new Sample(
                'Paginator',
                '',
                [
                    new CodeSample(
                        __DIR__ . '/paginator/paginate_by_size_per_page.php',
                        [new IOSample('/api/users?size=5', 'select * from "users" limit 6 offset 0')]
                    ),
                    new CodeSample(
                        __DIR__ . '/paginator/paginate_by_size_per_page_with_default.php',
                        [new IOSample('/api/users?size=', 'select * from "users" limit 6 offset 0')]
                    ),
                ]
            ),
        ];
    }
}
