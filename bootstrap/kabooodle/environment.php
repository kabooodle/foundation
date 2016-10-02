<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

// So basically L5's way of detecting the environment is stupid.
// It relies on the ENV file. To me, this is janky because
// I'm no longer really able to set different variables for multiple environments.
// L5 assumes you are either in production, or you're not... well we use
// 5-6 environments... So although this is rather manual and requires
// manual massaging now and then, who cares.  Also, using CLI sucks ass :(
$env = $app->detectEnvironment(function() use ($app){

    $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;
    if ($args && str_contains($args[0], 'phpunit')) {
        $env = 'testing';
    } else {
        $httpHost = strtolower(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : gethostname());
        switch ($httpHost) {
            case 'kabooodle.com':
                $env = 'production';
                break;

            case 'kabooodle-staging':
            case 'kabooodle.net':
            case '162.243.133.39':
                $env = 'staging';
                break;

            case 'ngrok' :
            case 'kabooodle.ngrok.io' :
            case '932b4484.ngrok.io' :
            case 'app.kabooodle.ngrok.io' :
                $env = 'ngrok';
                break;

            case 'orion' :
            case 'kabooodle.dev' :
            default :
                $env = 'local';
                break;
        }
    }

    // Overload existing properties
    if (file_exists(__DIR__ . '/../../.env.' . $env)) {
        $dotenv = new \Dotenv\Dotenv(__DIR__ . '/../../', '.env.' . $env);
        $dotenv->overload();
    }

    return $env;
});