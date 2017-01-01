<?php

$api->post('users/search', [
    'as' => 'users.search',
    'uses' => \Kabooodle\Http\Controllers\Api\User\QueryUser::class.'@query'
]);