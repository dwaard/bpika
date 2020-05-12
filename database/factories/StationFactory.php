<?php

$factory->define(App\Station::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name
    ];
});
