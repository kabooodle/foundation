<?php

Route::group(['middleware' => ['web'], 'domain' => getEnvDomain(true)], function(){
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'routes.php';
});

Route::group(['domain' => 'api.'.getEnvDomain(true)], function($route){
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'routes.php';
});
