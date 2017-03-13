<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use SammyK\LaravelFacebookSdk\LaravelUrlDetectionHandler;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use SammyK\LaravelFacebookSdk\LaravelPersistentDataHandler;

/**
 * Class FacebookServiceProvider
 * @package Kabooodle\Foundation\Providers
 */
class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Register the service providers.
     *
     * @return void
     */
    public function register()
    {
        // Main Service
        $this->app->bind('Kabooodle\Services\Social\Facebook\FacebookSdkService', function ($app) {
            $config = $app['config']->get('laravel-facebook-sdk.facebook_config');

            if (! isset($config['persistent_data_handler']) && isset($app['session.store'])) {
                $config['persistent_data_handler'] = new LaravelPersistentDataHandler($app['session.store']);
            }

            if (! isset($config['url_detection_handler'])) {
                $config['url_detection_handler'] = new LaravelUrlDetectionHandler($app['url']);
            }

            return new FacebookSdkService($app['config'], $app['url'], $config);
        });
    }
}
