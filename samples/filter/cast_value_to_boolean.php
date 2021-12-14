<?php

declare(strict_types=1);

use Zing\QueryBuilder\Enums\CastType;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;

QueryBuilder::fromBuilder(User::class, $request)
    ->enableFilters(Filter::exact('is_visible')->withCast(CastType::BOOLEAN))
    ->simplePaginate();
