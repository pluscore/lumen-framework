<?php

namespace Plus\Auth;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->routeMiddleware([
            'admin' => \Plus\Auth\Http\Middleware\Admin::class,
        ]);
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            return app('Plus\Auth\Services\Auth')->user();
        });
    }
}
