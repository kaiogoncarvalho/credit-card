<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\{CreditCard, Category};
use Faker\Generator as Faker;
use App\Enums\Brand;

$factory->define(CreditCard::class, function (Faker $faker) {
    $nameImage = $faker->word().".jpg";
    return [
        'name'  => $faker->unique->name,
        'slug'  => $faker->unique->name,
        'image' => $nameImage,
        'brand' => $faker->randomElement(Brand::getAll()),
        'category_id' => function() {
            return factory(Category::class)->create()->id;
        },
        'credit_limit' => $faker->optional()->randomFloat(2),
        'annual_fee'   => $faker->optional()->randomFloat(2),
    ];
});
