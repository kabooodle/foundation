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

        // Laravel is fucking weird about queues.
        // Default driver is "iron" but apparently this isn't looking for
        // the true "driver" but instead, the connection in this file named "iron".
        // For now, just in case, we will keep this one here.


        'iron-general' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'general-queue',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'general-queue',
            'encrypt' => true,
            'timeout' => 300
        ],


        ////////////////////---------
        // VIEW TRACKER QUEUES
        ////////////////////---------
        'iron-viewtracker' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'viewtracker-queue',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-viewtracker-b' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'viewtracker-queue-b',
            'encrypt' => true,
            'timeout' => 300
        ],



        ////////////////////---------
        // EMAIL QUEUES
        ////////////////////---------

        'iron-emails' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-emails-b' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue-b',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-emails-c' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue-c',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-emails-d' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue-d',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-emails-e' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue-e',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-emails-f' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue-f',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-emails-g' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'email-queue-g',
            'encrypt' => true,
            'timeout' => 300
        ],


        ////////////////////---------
        // FACEBOOK SCHEDULER QUEUES
        ////////////////////---------
        'iron-facebook-scheduler' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-scheduler-b' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue-b',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-scheduler-c' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue-c',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-scheduler-d' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue-d',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-scheduler-e' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-scheduler-queue-e',
            'encrypt' => true,
            'timeout' => 300
        ],




        ////////////////////---------
        // FACEBOOK LISTER QUEUES
        ////////////////////---------
        'iron-facebook-lister' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-b' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-b',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-c' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-c',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-d' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-d',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-e' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-e',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-f' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-f',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-g' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-g',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-h' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-h',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-i' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-i',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-lister-j' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-lister-queue-j',
            'encrypt' => true,
            'timeout' => 300
        ],


        ////////////////////---------
        // FACEBOOK SCHEDULER QUEUES
        ////////////////////---------
        'iron-facebook-deleter' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-deleter-queue',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-deleter-b' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-deleter-queue-b',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-deleter-c' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-deleter-queue-c',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-deleter-d' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-deleter-queue-d',
            'encrypt' => true,
            'timeout' => 300
        ],
        'iron-facebook-deleter-e' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1-2.iron.io',
            'token'   => env('IRON_QUEUE_TOKEN'),
            'project' => env('IRON_QUEUE_ID'),
            'queue'   => 'fb-deleter-queue-e',
            'encrypt' => true,
            'timeout' => 300
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
