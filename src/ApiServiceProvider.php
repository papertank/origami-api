<?php

namespace Origami\Api;

use League\Fractal\Manager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ApiServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/api.php' => config_path('api.php'),
        ]);

        Response::macro('api', function () {
            return app('api');
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/api.php',
            'api'
        );

        $this->app->singleton('Origami\Api\Api', function () {
            $config = app('config')->get('api', []);
            $response = app('Origami\Api\Response');
            $request = app('Illuminate\Http\Request');

            $api = new Api($config, $request, $response);

            if (isset($config['versions']['all'])) {
                $api->detectVersions();
            }

            return $api;
        });

        $this->app->bind('api', 'Origami\Api\Api');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('api');
    }
}
