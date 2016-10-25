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
    ],
    'allowedHeaders'      => ['*'],
    'allowedMethods' => ['GET', 'POST', 'PUT',  'DELETE', 'OPTIONS'],
    'exposedHeaders'      => [],
    'maxAge'              => 3600,
    'hosts'               => [],
];

