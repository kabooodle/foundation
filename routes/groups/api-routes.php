<?php

    $api->resource('groups', \Kabooodle\Http\Controllers\Api\Groups\GroupsApiController::class);
    $api->resource('groups.followers', \Kabooodle\Http\Controllers\Api\Groups\GroupsFollowersApiController::class);
    $api->post('inventory/{username}/associate', [
        'as' => 'inventory.associate.store',
        'uses' =>  \Kabooodle\Http\Controllers\Api\Inventory\InventoryApiController::class.'@associate',
    ]);
