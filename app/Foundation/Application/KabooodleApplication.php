<?php

namespace Kabooodle\Foundation\Application;

use Illuminate\Foundation\Application;

/**
 * Class KabooodleApplication
 * @package Kabooodle\Foundation\Application
 */
class KabooodleApplication extends Application
{
    /**
     * @var string
     */
    const APP_VERSION = '0.0.3';

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
