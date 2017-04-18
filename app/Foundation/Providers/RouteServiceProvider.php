<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * Class RouteServiceProvider
 * @package Kabooodle\Foundation\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapWorkerRoutes($router);
        $this->mapWebRoutes($router);
        $this->mapApiRoutes($router);
    }

    /**
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function mapWorkerRoutes(Router $router)
    {
        $router->group([], function ($route) {
            require base_path('routes/workers/routes.php');
        });
    }

    /**
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group([
            'domain' => 'www.'.getEnvDomain(true),
            'middleware' => ['web'],
        ], function ($route) {
            require base_path('routes/web/routes.php');
        });
    }

    /**
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapApiRoutes(Router $router)
    {
        $router->group([
            'domain' => 'api.'.getEnvDomain(true)
        ], function($route){
            require base_path('routes/api/routes.php');
        });
    }
}
