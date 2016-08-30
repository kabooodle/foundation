<?php

    $api->resource('groups', \Kabooodle\Http\Controllers\Api\Groups\GroupsApiController::class);
    $api->resource('groups.followers', \Kabooodle\Http\Controllers\Api\Groups\GroupsFollowersApiController::class);
