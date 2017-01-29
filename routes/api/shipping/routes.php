<?php

    $api->post('shipping/parcel/create', [
        'as' => 'shipping.parcel.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Shipping\ShippingParcelController::class . '@store'
    ]);

    $api->post('shipping/label/create', [
        'as' => 'shipping.label.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Shipping\ShippingLabelController::class . '@store'
    ]);
