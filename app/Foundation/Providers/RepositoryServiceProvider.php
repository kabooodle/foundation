<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Kabooodle\Services\User\UserService;
use Kabooodle\Repositories\User\UserRepository;
use Kabooodle\Services\Listings\ListingsService;
use Kabooodle\Repositories\Claims\ClaimsRepository;
use Kabooodle\Repositories\Listings\ListingsRepository;
use Kabooodle\Repositories\User\UserRepositoryInterface;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Repositories\Claims\ClaimsRepositoryInterface;
use Kabooodle\Repositories\Listings\ListingsRepositoryInterface;

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
        $this->registerClaimsServices();
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

    public function registerClaimsServices()
    {
        $this->app->bind(ClaimsRepositoryInterface::class, ClaimsRepository::class);
    }
}
