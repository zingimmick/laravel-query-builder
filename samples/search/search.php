<?php

declare(strict_types=1);

use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;

QueryBuilder::fromBuilder(User::class, $request)
    ->searchable(['name', 'email'])
    ->simplePaginate();
