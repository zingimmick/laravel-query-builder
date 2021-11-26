<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Zing\QueryBuilder\Filter;
use Zing\QueryBuilder\QueryBuilder;
use Zing\QueryBuilder\Tests\Models\Order;
use Zing\QueryBuilder\Tests\Models\User;

class FilterExactTest extends TestCase
{
    use WithFaker;

    public function testExpression(): void
    {
        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'created_at' => Carbon::yesterday()->setTimeFromTimeString($this->faker->time()),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 2)
        );
        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'created_at' => Carbon::today()->setTimeFromTimeString($this->faker->time()),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 3)
        );
        request()
            ->merge([
                'created_date' => Carbon::yesterday()->toDateString(),
            ]);
        self::assertSame(
            2,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::exact('created_date', DB::raw('date(created_at)')))
                ->count()
        );
    }

    public function testDate(): void
    {
        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'created_at' => Carbon::yesterday()->setTimeFromTimeString($this->faker->time()),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 2)
        );

        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'created_at' => Carbon::today()->setTimeFromTimeString($this->faker->time()),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 3)
        );
        request()
            ->merge([
                'created_date' => Carbon::yesterday()->toDateString(),
            ]);
        self::assertSame(
            2,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::date('created_date', 'created_at'))
                ->count()
        );
        request()
            ->merge([
                'created_date' => Carbon::yesterday(),
            ]);
        self::assertSame(
            2,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::date('created_date', 'created_at'))
                ->count()
        );
        request()
            ->merge([
                'created_date' => [Carbon::yesterday(), today()->toDateString()],
            ]);
        self::assertSame(
            5,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::date('created_date', 'created_at'))
                ->count()
        );
    }

    public function testBoolean(): void
    {
        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'created_at' => Carbon::yesterday()->setTimeFromTimeString($this->faker->time()),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 2)
        );
        array_map(
            function (): void {
                Order::query()->create(
                    [
                        'user_id' => User::query()->create([
                            'name' => $this->faker->name(),
                        ])->getKey(),
                        'created_at' => Carbon::today()->setTimeFromTimeString($this->faker->time()),
                        'number' => $this->faker->randomNumber(),
                    ]
                );
            },
            range(1, 3)
        );
        request()
            ->merge([
                'is_today' => 1,
            ]);
        self::assertSame(
            2,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::boolean('is_today', function ($query) {
                    return $query->whereDate('created_at', Carbon::yesterday());
                }, function ($query) {
                    return $query->whereDate('created_at', '!=', Carbon::yesterday());
                }))
                ->count()
        );
        request()
            ->merge([
                'is_today' => 0,
            ]);
        self::assertSame(
            3,
            QueryBuilder::fromBuilder(Order::class, request())
                ->enableFilters(Filter::boolean('is_today', function ($query) {
                    return $query->whereDate('created_at', Carbon::yesterday());
                }, function ($query) {
                    return $query->whereDate('created_at', '!=', Carbon::yesterday());
                }))
                ->count()
        );
    }
}
