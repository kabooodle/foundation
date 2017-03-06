<?php

$api->get('referrals', [
    'as' => 'referrals.index',
    'uses' => \Kabooodle\Http\Controllers\Api\Referrals\ReferralsApiController::class . '@index'
]);