<?php

declare(strict_types=1);

namespace Zing\QueryBuilder\Tests\Models;

use Zing\QueryBuilder\Tests\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
