<?php

namespace Plus\Auth\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Request;

class BackendService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->clientIsBackendService()) {
            return $next($request);
        }

        return abort(403);
    }

    /**
     * Determine if the client is a backend service.
     *
     * @return boolean
     */
    public function clientIsBackendService()
    {
        // Inner backend
        return Request::header('host') === config('service.backend_host')
            || (app()->environment() !== 'production' && Request::header('client-type') === 'backend');
    }
}
