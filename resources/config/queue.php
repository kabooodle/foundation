<?php

return [

    'max_retries' => 3,

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "null", "sync", "database", "beanstalkd", "sqs", "redis"
    |
    */

    'default' => env('QUEUE_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'expire' => 60,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'ttr' => 60,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => 'your-public-key',
            'secret' => 'your-secret-key',
            'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
            'queue' => 'your-queue-name',
            'region' => 'us-east-1',
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'expire' => 60,
        ],


        'iron' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue',
            'encrypt' => true,
            'timeout' => 60
        ],


        'iron-emails-1' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue-1',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-emails-2' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue-2',
            'encrypt' => true,
            'timeout' => 60
        ],


        // FACEBOOK SCHEDULER QUEUES
        'iron-facebook-scheduler-1' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue-1',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-scheduler-2' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue-2',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-scheduler-3' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue-3',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-scheduler-4' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue-4',
            'encrypt' => true,
            'timeout' => 60
        ],


        // FACEBOOK LISTER QUEUES
        'iron-facebook-lister-1' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-1',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-2' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-2',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-3' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-3',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-4' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-4',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-5' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-5',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-6' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-6',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-7' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-7',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-8' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-8',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-9' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-9',
            'encrypt' => true,
            'timeout' => 60
        ],
        'iron-facebook-lister-10' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-10',
            'encrypt' => true,
            'timeout' => 60
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
