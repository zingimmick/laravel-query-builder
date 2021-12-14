<?php

declare(strict_types=1);

use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;

QueryBuilder::fromBuilder(User::class, $request)
    ->searchable(['name'])
    ->enableFilters(['is_visible', 'status'])
    ->enableSorts(['created_at'])
    ->enablePaginator()
    ->simplePaginate();
