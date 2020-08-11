<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Zing\QueryBuilder\Tests\Models\User;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    User::class,
    function (Faker $faker) {
        return [
            'name' => $faker->name,
        ];
    }
);
