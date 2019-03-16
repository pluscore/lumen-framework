<?php

namespace Plus\Testing;

use App\Exceptions\Handler;
use Plus\Auth\Facades\Auth;
use Illuminate\Contracts\Debug\ExceptionHandler;
use PHPUnit\Framework\Assert;
use Plus\Auth\User;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    /**
     * Mock a given class instance in the container.
     */
    protected function mock($class, $mockHandler)
    {
        $mock = Mockery::mock($class);

        $mockHandler($mock);

        $this->app->instance($class, $mock);
    }

    /**
     * Enable exception handling.
     * 
     * return $this
     */
    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new \App\Exceptions\Handler);

        return $this;
    }

    /**
     * Disable exception handling.
     * 
     * return $this
     */
    protected function withoutExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() { }
            public function report(\Exception $e) { }
            public function render($request, \Exception $e) { throw $e; }
        });

        return $this;
    }

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
        Auth::shouldReceive('user')->once()->andReturn(null);

        return $this;
    }

    public function actingAsUser()
    {
        $this->user = $this->makeUser();

        Auth::shouldReceive('user')->once()->andReturn($this->user);

        return $this;
    }

    public function actingAsAdmin()
    {
        $this->user = $this->makeUser(['is_admin' => true]);

        Auth::shouldReceive('user')->once()->andReturn($this->user);

        return $this;
    }

    public function actingAsOfficerOf($publisherId)
    {
        $this->user = $this->makeUser([
            'publishers' => [
                ['id' => $publisherId]
            ]
        ]);

        Auth::shouldReceive('user')->once()->andReturn($this->user);

        return $this;
    }

    public function seeJsonSubset($data, $message = '')
    {
        Assert::assertArraySubset($data, $this->response->getData(true), $message);

        return $this;
    }

    public function seeValidationError($field)
    {
        $this->seeStatusCode(422)->seeJson();

        $this->seeJsonSubset(['errors' => []], 'No validation errors have seen.');

        $this->assertTrue(isset($this->response->getData()->errors->$field), 'No validation error have seen for '.$field);
    }
}
