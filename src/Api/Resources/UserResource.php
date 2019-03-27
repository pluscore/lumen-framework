<?php

namespace Plus\Api\Resources;

use Faker\Generator;
use Plus\Api\Resource;

class UserResource extends Resource
{
    protected $path = 'http://server/api/users';

    public function mock(Generator $faker)
    {
        return [
            'id' => $faker->uuid,
            'name' => $faker->name,
            'email' => $faker->email,
        ];
    }
}
