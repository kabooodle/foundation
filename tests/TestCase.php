<?php

namespace Kabooodle\Tests;

use Illuminate\Foundation\Testing\TestCase as L_TestCase;

/**
 * Class TestCase
 * @package Kabooodle\Tests
 */
class TestCase extends L_TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://kabooodle.dev';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require_once __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
