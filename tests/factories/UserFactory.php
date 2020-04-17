<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Zing\QueryBuilder\Tests\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
