<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Application;

use Illuminate\Foundation\Application;
use Illuminate\Routing\RoutingServiceProvider;
use Kabooodle\Foundation\Providers\EventDispatcherServiceProvider;

/**
 * Class KabooodleApplication
 * @package Kabooodle\Foundation\Application
 */
class KabooodleApplication extends Application
{
    /**
     * @var string
     */
    const APP_VERSION = '0.10.3';
    const RELEASE_VERSION = '0.10.1';

    /**
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventDispatcherServiceProvider($this));

        $this->register(new RoutingServiceProvider($this));
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * Get the path to the database directory.
     *
     * @return string
     */
    public function databasePath()
    {
        return $this->databasePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'database';
    }

    /**
     * Get the path to the language files.
     *
     * @return string
     */
    public function viewsPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
    }
}
