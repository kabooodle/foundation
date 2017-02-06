<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'workers' . DIRECTORY_SEPARATOR . 'routes.php';

Route::get('privacy', function(){
    return view('content.privacy');
});

// Routes registered to api.kabooodle.tld
Route::group(['domain' => 'api.'.getEnvDomain(true)], function($route){
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'routes.php';
});

// routes registered to domain.tld
Route::group(['middleware' => ['web']], function(){
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'routes.php';
});
