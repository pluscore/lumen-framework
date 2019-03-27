<?php

namespace Plus\Api;

use Faker\Generator;
use Illuminate\Contracts\Support\Arrayable;
use Zttp\Zttp;

abstract class Resource implements Arrayable
{
    static $faker;
    private $attributes;

    protected $path;
    public $query;

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
        $this->query = [];
    }

    public static function fake()
    {
        if (! static::$faker) {
            static::$faker = new ResourceFaker(new static);
        }

        return static::$faker;
    }

    public function path()
    {
        $query = http_build_query($this->query);

        return $this->path.($query ? "?{$query}" : '');
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function newRequest()
    {
        return new Request(new static);
    }

    public function mock(Generator $faker)
    {
        throw new \Exception('Mock not supported in this resource.');
    }

    public function __get($field)
    {
        return $this->attributes[$field];
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(
            [$this->newRequest(), $name], $arguments
        );
    }
}
