<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;

class FilterExactTest extends TestCase
{
    use WithFaker;

    public function testExpression(): void
    {
        Order::factory()->times(2)->create(
            [
                'created_at' => Carbon::yesterday()->setTimeFromTimeString($this->faker->time()),
            ]
        );
        Order::factory()->times(3)->create(
            [
                'created_at' => Carbon::today()->setTimeFromTimeString($this->faker->time()),
            ]
        );
        request()->merge(
            [
                'created_date' => Carbon::yesterday()->toDateString(),
            ]
        );
        self::assertSame(
            2,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::exact('created_date', DB::raw('date(created_at)')))
                ->count()
        );
    }

    public function testDate(): void
    {
        Order::factory()->times(2)->create(
            [
                'created_at' => Carbon::yesterday()->setTimeFromTimeString($this->faker->time()),
            ]
        );
        Order::factory()->times(3)->create(
            [
                'created_at' => Carbon::today()->setTimeFromTimeString($this->faker->time()),
            ]
        );
        request()->merge(
            [
                'created_date' => Carbon::yesterday()->toDateString(),
            ]
        );
        self::assertSame(
            2,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::date('created_date', 'created_at'))
                ->count()
        );
        request()->merge(
            [
                'created_date' => Carbon::yesterday(),
            ]
        );
        self::assertSame(
            2,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::date('created_date', 'created_at'))
                ->count()
        );
        request()->merge(
            [
                'created_date' => [Carbon::yesterday(), today()->toDateString()],
            ]
        );
        self::assertSame(
            3,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::date('created_date', 'created_at'))
                ->count()
        );
    }
}
