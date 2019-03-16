<?php

namespace Plus\Auth\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'Plus\Auth\Services\Auth';
    }
}
