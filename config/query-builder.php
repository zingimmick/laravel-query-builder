<?php

declare(strict_types=1);

return [
    'per_page' => [
        'key' => 'per_page',
        'default' => 15,
    ],
    'builders' => [
        \Illuminate\Database\Eloquent\Builder::class => \Zing\QueryBuilder\QueryBuilder::class,
    ],
];
