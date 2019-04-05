<?php

namespace Plus\Testing\Traits;

use Plus\Auth\Facades\Auth;
use Plus\Auth\User;

trait InteractsWithAuthService
{
	public function makeUser($overrides = [])
    {
        $faker = app(\Faker\Generator::class);

        return new User(array_merge([
            'id' => $faker->uuid,
            'name' => $faker->name,
            'email' => $faker->email,
            'publishers' => []
        ], $overrides));
    }

    public function actingAsGuest()
    {
        Auth::shouldReceive('user')->andReturn(null);

        return $this;
    }

    public function actingAsUser()
    {
        $this->user = $this->makeUser();

        Auth::shouldReceive('user')->andReturn($this->user);

        return $this;
    }

    public function actingAsAdmin()
    {
        $this->user = $this->makeUser(['is_admin' => true]);

        Auth::shouldReceive('user')->andReturn($this->user);

        return $this;
    }

    public function actingAsOfficerOf($publisherId)
    {
        $this->user = $this->makeUser([
            'publishers' => [
                ['id' => $publisherId]
            ]
        ]);

        Auth::shouldReceive('user')->andReturn($this->user);

        return $this;
    }
}
