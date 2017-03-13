<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Kabooodle\Repositories\Listings\ListingsRepository;
use Kabooodle\Repositories\Listings\ListingsRepositoryInterface;
use Kabooodle\Services\Listings\ListingsService;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Services\User\UserService;
use URL;
use Illuminate\Support\ServiceProvider;
use Kabooodle\Repositories\User\UserRepository;
use Kabooodle\Repositories\User\UserRepositoryInterface;

/**
 * Class RepositoryServiceProvider
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserService::class, function($app){
            return new UserService($app->make(UserRepositoryInterface::class));
        });

        $this->registerListingsServices();
    }

    public function registerListingsServices()
    {
        $this->app->bind(ListingsRepositoryInterface::class, ListingsRepository::class);

        $this->app->bind(ListingsService::class, function($app){
            return new ListingsService(
                $app->make(ListingsRepositoryInterface::class),
                $app->make(FacebookSdkService::class)
            );
        });
    }
}
