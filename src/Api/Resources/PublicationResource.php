<?php

namespace Plus\Api\Resources;

use Faker\Generator;
use Plus\Api\Resource;

class PublicationResource extends Resource
{
    /**
     * The url to the resource.
     *
     * @var string
     */
    public $url = 'http://server/api/publications/backend';

    /**
     * Generate a mock attributes for a resource.
     *
     * @param  Generator $faker
     * @return array
     */
    public static function mock(Generator $faker)
    {
        return [
            'id' => $faker->uuid,
            'title' => $faker->sentence(3),
        ];
    }
}
