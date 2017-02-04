<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

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
    }
}
