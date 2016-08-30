<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |

     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => true,
    'allowedOrigins'      => ['http://kabooodle.dev', 'http://api.kabooodle.dev', 'http://www.kabooodle.dev', 'http://kabooodle.net', 'http://api.kabooodle.net', 'http://www.kabooodle.net'],
    'allowedHeaders'      => ['X-Kabooodle-Token', 'Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Authorization'],
    'allowedMethods' =>     ['GET', 'POST', 'PUT',  'DELETE', 'OPTIONS'],
    'exposedHeaders'      => [],
    'maxAge'              => 3600,
    'hosts'               => [],
];

