<?php


    $api->post('social/refresh', [
        'as' => 'social.refresh',
        'uses' => \Kabooodle\Http\Controllers\Api\Social\RefreshFacebookGroupsController::class.'@refresh'
    ]);
