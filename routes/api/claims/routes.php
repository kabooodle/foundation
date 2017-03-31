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