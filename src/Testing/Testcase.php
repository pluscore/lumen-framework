<?php

namespace Plus\Testing;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use PHPUnit\Framework\Assert as PHPUnit;
use Mockery;

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    use Traits\InteractsWithAuthService;

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
            public function __construct()
            {
            }
            public function report(\Exception $e)
            {
            }
            public function render($request, \Exception $e)
            {
                throw $e;
            }
        });

        return $this;
    }

    public function seeJsonResourceCollection($collection)
    {
        PHPUnit::assertCount(count($collection), $this->response->getData(true)['data']);

        foreach ($collection as $index => $item) {
            PHPUnit::assertEquals($item->id, $this->response->getData(true)['data'][$index]['id']);
        }

        return $this;
    }

    public function seeJsonSubset($data, $message = '')
    {
        PHPUnit::assertArraySubset($data, $this->response->getData(true), $message);

        return $this;
    }

    public function seeValidationError($field)
    {
        $this->seeStatusCode(422)->seeJson();

        $this->seeJsonSubset(['errors' => []], 'No validation errors have seen.');

        $this->assertTrue(isset($this->response->getData()->errors->$field), 'No validation error have seen for '.$field);
    }
}
