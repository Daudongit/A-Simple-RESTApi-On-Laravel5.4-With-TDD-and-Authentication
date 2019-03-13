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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'first_name'=> $faker->firstName,
        'last_name'=>$faker->lastName,
        'date_of_birth'=>$faker->dateTimeInInterval('-30 years','+ 5 years'),
        'email'=>$faker->unique()->safeEmail,
        'username'=>$faker->unique()->name,
        'password'=>$password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Country::class, function (Faker\Generator $faker) {
    return [
        'name'=>$faker->unique()->country,
        'continent'=>$faker->word
    ];
});
