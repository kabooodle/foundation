<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

use Kabooodle\Http\Controllers\Api\Claims\ClaimsMerchantApiController;

$api->get('claims', [
    'as' => 'claims.index',
    'uses' => ClaimsMerchantApiController::class.'@index'
]);
$api->post('claims/bulk/accept', [
    'as' => 'claims.accept',
    'uses' => ClaimsMerchantApiController::class.'@accept'
]);
$api->post('claims/bulk/return', [
    'as' => 'claims.return',
    'uses' => ClaimsMerchantApiController::class.'@reject'
]);
$api->post('claims/bulk/label', [
    'as' => 'claims.label',
    'uses' => ClaimsMerchantApiController::class.'@label'
]);