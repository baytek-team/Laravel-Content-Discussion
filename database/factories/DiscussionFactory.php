<?php

use Baytek\Laravel\Content\Types\Discussion\Models\Discussion;
use Baytek\Laravel\Content\Types\Discussion\Models\Topic;

/**
 * Discussions Categories
 */
$factory->define(Topic::class, function (Faker\Generator $faker) {

    $title = ucwords(implode(' ', $faker->unique()->words(rand(1,2))));

    return [
        'key' => str_slug($title),
        'title' => $title,
        'content' => null,
        'status' => Topic::APPROVED,
        'language' => App::getLocale(),
    ];
});

/**
 * Discussions Items
 */
$factory->define(Discussion::class, function (Faker\Generator $faker) {

    $title = $faker->sentence();

    return [
        'key' => str_slug($title),
        'title' => $title,
        'content' => $faker->paragraph(),
        'status' => Discussion::APPROVED,
        'language' => App::getLocale(),
    ];
});
