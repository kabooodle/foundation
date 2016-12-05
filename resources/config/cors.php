<?php

return [
    'supportsCredentials' => true,
    'allowedOrigins'      => [
        'http://app.kabooodle.ngrok.io',
        'http://api.kabooodle.ngrok.io',

        'http://app.kabooodle.dev',
        'http://api.kabooodle.dev',

        'http://api.kabooodle.net',
        'http://app.kabooodle.net',

        'http://app.kabooodle.com',
        'http://api.kabooodle.com',

        'http://d1xa16vtrvw19v.cloudfront.net', // net
        'http://d1o4ibed66ebi1.cloudfront.net', // com
        'http://d2jx59dcc1ko56.cloudfront.net', // dev
    ],
    'allowedHeaders'      => ['*'],
    'allowedMethods' => ['GET', 'POST', 'PUT',  'DELETE', 'OPTIONS'],
    'exposedHeaders'      => [],
    'maxAge'              => 3600,
    'hosts'               => [],
];

