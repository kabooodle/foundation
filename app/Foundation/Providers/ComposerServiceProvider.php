<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

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
        $factory->composer('layouts.header._htmlheader', CurrentUserComposer::class);
    }

    public function register()
    {
        //
    }
}
