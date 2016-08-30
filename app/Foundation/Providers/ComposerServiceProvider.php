<?php

namespace Kabooodle\Foundation\Providers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use Kabooodle\Composers\CurrentUserComposer;

/**
 * Class RouteServiceProvider
 * @package Kabooodle\Foundation\Providers
 */
class ComposerServiceProvider extends ServiceProvider
{
    public function boot(Factory $factory)
    {
        $factory->composer('*', CurrentUserComposer::class);
    }

    public function register()
    {
        //
    }
}
