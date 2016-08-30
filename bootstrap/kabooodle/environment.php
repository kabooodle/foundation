<?php

$env = $app->detectEnvironment(function() use ($app){
    $httpHost = ( isset($_SERVER['HTTP_HOST']) ) ? $_SERVER['HTTP_HOST'] : gethostname();
    switch ($httpHost) {
        case 'kabooodle.com':
            $env = 'production';
            break;

        case 'kabooodle-staging':
        case 'kabooodle.net':
        case '162.243.133.39':
            $env = 'staging';
            break;

        case 'kabooodle.dev' :
        default :
            $env = 'local';
            break;
    }


    // Overload existing properties
    if (file_exists(__DIR__ . '/../../.env.' . $env)) {
        $dotenv = new \Dotenv\Dotenv(__DIR__ . '/../../', '.env.' . $env);
        $dotenv->overload();
    }

    return $env;
});