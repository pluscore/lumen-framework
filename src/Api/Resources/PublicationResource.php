<?php

namespace Plus\Api\Resources;

use Faker\Generator;
use Plus\Api\Resource;

class PublicationResource extends Resource
{
    protected $path = 'http://server/api/catalog/backend/publications';

    public static function mock(Generator $faker)
    {
        return [
            'id' => $faker->uuid,
            'title' => $faker->title,
        ];
    }
}
