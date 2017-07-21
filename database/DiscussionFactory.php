<?php

/**
 * Discussions Categories
 */
$factory->define(App\ContentTypes\Discussions\Models\Topic::class, function (Faker\Generator $faker) {

    $title = ucwords(implode(' ', $faker->unique()->words(rand(1,2))));

    return [
        'key' => str_slug($title),
        'title' => $title,
        'content' => null,
        'status' => App\ContentTypes\Discussions\Models\Topic::APPROVED,
        'language' => App::getLocale(),
    ];
});

/**
 * Discussions Items
 */
$factory->define(App\ContentTypes\Discussions\Models\Discussion::class, function (Faker\Generator $faker) {

    $title = $faker->sentence();

    return [
        'key' => str_slug($title),
        'title' => $title,
        'content' => $faker->paragraph(),
        'status' => App\ContentTypes\Discussions\Models\Discussion::APPROVED,
        'language' => App::getLocale(),
    ];
});
