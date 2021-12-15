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
            new Sample('Basic usage', '', [require __DIR__ . '/basic/basic/sample.php']),
            new Sample('Search', '', [require __DIR__ . '/search/search/sample.php']),
            (new Sample('Search', 'Composite search', [
                require __DIR__ . '/search/composite_search/sample.php',
            ]))->description('⚠️ The filter with default value is not supported yet.'),
            new Sample('Filter', '', [
                require __DIR__ . '/filter/filter_column_contains_property_value/sample.php',
                require __DIR__ . '/filter/filter_column_equals_to_property_value/sample.php',
                require __DIR__ . '/filter/filter_with_scope_and_default/sample.php',
            ]),
            (new Sample('Filter', 'Typed filter', [
                require __DIR__ . '/filter/filter_orders_with_type_and_value/sample.php',
            ]))->description('⚠️ The filter with default value is not supported yet.'),
            new Sample('Filter', 'Flagged filter', [
                require __DIR__ . '/filter/filter_orders_match_any_filters/sample.php',
            ]),
            new Sample('Filter', 'Cast Input(Skip auto cast)', [
                require __DIR__ . '/filter/cast_value_to_boolean/sample.php',
                require __DIR__ . '/filter/cast_value_force_to_string/sample.php',
            ]),
            new Sample('Sort', '', [require __DIR__ . '/sort/sort_users_by_created_date/sample.php']),
            new Sample('Paginator', '', [
                require __DIR__ . '/paginator/paginate_by_size_per_page/sample.php',
                require __DIR__ . '/paginator/paginate_by_size_per_page_with_default/sample.php',
            ]),
        ];
    }
}
