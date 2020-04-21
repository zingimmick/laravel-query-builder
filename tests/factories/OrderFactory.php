<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Zing\QueryBuilder\Tests\Order;
use Zing\QueryBuilder\Tests\User;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Order::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->getKey();
        },
        'number' => $faker->randomNumber(),
    ];
});
