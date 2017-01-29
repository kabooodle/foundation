<?php


    $api->post('shipping/parcel/create', [
        'as' => 'shipping.parcel.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Shipping\ShippingParcelController::class . '@store'
    ]);

