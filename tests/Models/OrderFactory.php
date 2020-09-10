<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Zing\QueryBuilder\Tests\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'number' => $this->faker->randomNumber(),
        ];
    }
}
