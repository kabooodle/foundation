<?php

$api->get('users/{username}/listables', [
    'as' => 'listables.index',
    'uses' =>  \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@index',
]);
$api->get('users/{username}/listables/detailed', [
    'as' => 'listables.detailed.index',
    'uses' =>  \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@detailed',
]);
$api->get('users/{username}/listables/{groupingId}', [
    'as' => 'listables.show',
    'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@show'
]);
$api->get('users/{username}/listables/{groupingId}/comments', [
    'as' => 'listables.comments.index',
    'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesCommentsController::class.'@index'
]);

$api->group(['middleware' => 'jwt.auth'], function($api) {
    $api->put('listables/{id}/archive', [
        'as' => 'listables.archive',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@archive'
    ]);
    $api->delete('listables/{id}/archive', [
        'as' => 'listables.activate',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@activate'
    ]);
    // Inventory Groupings
    $api->post('users/{username}/listables', [
        'as' => 'listables.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@store'
    ]);
    $api->put('users/{username}/listables/{id}', [
        'as' => 'listables.update',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@update'
    ]);
    $api->delete('users/{username}/listables/{id}', [
        'as' => 'listables.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@destroy'
    ]);
    $api->post('users/{username}/listables/{id}/claims', [
        'as' => 'listables.claims.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@claim'
    ]);

    // Comments
    $api->post('users/{username}/listables/{groupingId}/comments', [
        'as' => 'listables.comments.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesCommentsController::class.'@store'
    ]);
    $api->put('users/{username}/listables/{groupingId}/comments/{commentId}', [
        'as' => 'listables.comments.update',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesCommentsController::class.'@update'
    ]);
    $api->delete('users/{username}/listables/{groupingId}/comments/{commentId}', [
        'as' => 'listables.comments.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\Listables\ListablesCommentsController::class.'@destroy'
    ]);

    // Associations
    $api->post('users/{username}/listables/{groupingId}/associate', [
        'as' => 'listables.associate.store',
        'uses' =>  \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@associate',
    ]);
    $api->delete('users/{username}/listables/{groupingId}/associate/{id}', [
        'as' => 'listables.associate.destroy',
        'uses' =>  \Kabooodle\Http\Controllers\Api\Listables\ListablesController::class.'@destroyAssociation',
    ]);
});
