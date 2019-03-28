<?php

namespace Plus\Api\Resources;

use Faker\Generator;
use Plus\Api\Resource;

class UserResource extends Resource
{
    /**
     * The url to the resource.
     *
     * @var string
     */
    public $url = 'http://server/api/users';

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
            'name' => $faker->name,
            'email' => $faker->email,
            'avatar_path' => $faker->imageUrl,
            'profile_picture' => [
                'sm' => $faker->imageUrl,
                'md' => $faker->imageUrl,
            ]
        ];
    }
}
