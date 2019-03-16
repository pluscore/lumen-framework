<?php

namespace Plus\Auth\Services;

use Plus\Auth\User;
use Illuminate\Support\Facades\Request;
use Zttp\Zttp;

class Auth
{
    /**
     * Get the current user.
     *
     * @return
     */
    public function user()
    {
        $response = Zttp::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => Request::header('Authorization'),
            'Cookie' => Request::header('Cookie'),
        ])->get('http://server/api/user', ['include' => ['publishers']]);

        return $response->isOk() ? new User($response->json()['data']) : null;
    }
}
