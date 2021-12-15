<?php

declare(strict_types=1);

use Zing\QueryBuilder\Paginator;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;

QueryBuilder::fromBuilder(User::class, $request)
    ->enablePaginator(Paginator::name('size')->default(5))
    ->simplePaginate();
