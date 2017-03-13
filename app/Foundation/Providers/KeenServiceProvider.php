<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use KeenIO\Client\KeenIOClient;
use Illuminate\Support\ServiceProvider;

/**
 * Class KeenServiceProvider
 */
class KeenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(KeenIOClient::class, function($app){
            return KeenIOClient::factory([
                'masterKey' => env('KEENIO_MASTERKEY'),
                'projectId' => env('KEENIO_PROJECTID'),
                'writeKey' => env('KEENIO_WRITEKEY'),
                'readKey' => env('KEENIO_READKEY'),
            ]);
        });
    }
}
