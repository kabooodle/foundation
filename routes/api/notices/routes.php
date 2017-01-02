<?php

    $api->get('notices', [
        'as' => 'notices.index',
        'uses' => \Kabooodle\Http\Controllers\Api\Notices\NoticesApiController::class . '@index'
    ]);

    $api->post('notices/all/mark_as_read', [
        'as' => 'notices.all.mark_as_read',
        'uses' => \Kabooodle\Http\Controllers\Api\Notices\NoticesApiController::class . '@markAsRead'
    ]);
