<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Zing\QueryBuilder\Paginator;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\User;

/**
 * @internal
 */
final class PaginatorTest extends TestCase
{
    public function testPerPage(): void
    {
        $builder = QueryBuilder::fromBuilder(User::class, request());
        $this->assertSame(config('query-builder.per_page.default'), $builder->paginate()->perPage());
        $builder = QueryBuilder::fromBuilder(User::class, request())->enablePaginator();
        $this->assertSame(15, $builder->paginate()->perPage());
        $perPage = 10;
        request()
            ->merge([
                'per_page' => $perPage,
            ]);
        $builder = QueryBuilder::fromBuilder(User::class, request())->enablePaginator();
        $this->assertSame($perPage, $builder->paginate()->perPage());
    }

    public function testPerPageName(): void
    {
        $builder = QueryBuilder::fromBuilder(User::class, request());
        $this->assertSame(config('query-builder.per_page.default'), $builder->paginate()->perPage());
        $perPage = 10;
        request()
            ->merge([
                'size' => $perPage,
            ]);
        $builder = QueryBuilder::fromBuilder(User::class, request())->enablePaginator('size');
        $this->assertSame($perPage, $builder->paginate()->perPage());
    }

    public function testPaginator(): void
    {
        $builder = QueryBuilder::fromBuilder(User::class, request());
        $this->assertSame(config('query-builder.per_page.default'), $builder->paginate()->perPage());
        request()
            ->merge([]);
        $builder = QueryBuilder::fromBuilder(User::class, request())->enablePaginator(
            Paginator::name('size')->default(5)
        );
        $this->assertSame(5, $builder->paginate()->perPage());
        request()
            ->merge([
                'size' => null,
            ]);
        $builder = QueryBuilder::fromBuilder(User::class, request())->enablePaginator(
            Paginator::name('size')->default(5)
        );
        $this->assertSame(5, $builder->paginate()->perPage());
    }

    public function testSimplePaginate(): void
    {
        $builder = QueryBuilder::fromBuilder(User::class, request());
        $this->assertSame(config('query-builder.per_page.default'), $builder->simplePaginate()->perPage());
        $perPage = 10;
        request()
            ->merge([
                'per_page' => $perPage,
            ]);
        $builder = QueryBuilder::fromBuilder(User::class, request())->enablePaginator('per_page');
        $this->assertSame($perPage, $builder->simplePaginate()->perPage());
    }
}
