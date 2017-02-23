<?php

$api->get('users/{username}/listables', [
    'as' => 'listables.index',
    'uses' =>  \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@index',
]);
