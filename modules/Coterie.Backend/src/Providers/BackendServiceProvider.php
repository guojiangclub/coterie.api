<?php

namespace iBrand\Coterie\Backend\Providers;

use iBrand\Backend\Models\Admin;
use iBrand\Coterie\Backend\Http\Middleware\Authenticate;
use iBrand\Coterie\Backend\Observers\AdminCreateObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Cookie;

class BackendServiceProvider extends ServiceProvider
{

    protected $namespace = 'iBrand\Coterie\Backend\Http\Controllers';


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'account-backend');

        if (!$this->app->routesAreCached()) {
            $this->mapWebRoutes();
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../resources/assets' => public_path('assets/account-backend'),
            ], 'account-backend-assets');
        }

        Admin::observe(AdminCreateObserver::class);

    }

    public function register()
    {
        //app('router')->aliasMiddleware('admin.auth', Authenticate::class);
    }


    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web', 'admin'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require __DIR__ . '/../Http/routes.php';
        });
    }
}
