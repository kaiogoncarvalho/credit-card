<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Enums\Scope;

$factory->define(
    App\Models\User::class,
    function (Faker\Generator $faker) {
        return [
            'name'     => $faker->firstName,
            'email'    => $faker->email,
            'password' => Hash::make($faker->password),
            'scopes'   => $faker->randomElements(Scope::getAll(), $faker->numberBetween(1,2))
        ];
    }
);
