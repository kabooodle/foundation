<?php



$api->group(['middleware' => 'jwt.auth'], function ($api) {

    $api->post('groups', [
        'as' => 'groups.store',
        'uses' => \Kabooodle\Http\Controllers\Api\Groups\GroupsApiController::class.'@store'
    ]);


    $api->post('user/{id}/followers', [
        'as' => 'user.followers.store',
        'uses' => \Kabooodle\Http\Controllers\Api\User\FollowsController::class.'@store'
    ]);
    $api->delete('user/{id}/followers', [
        'as' => 'user.followers.destroy',
        'uses' => \Kabooodle\Http\Controllers\Api\User\FollowsController::class.'@destroy'
    ]);
    $api->post('sales/filter', [
        'as' => 'sales.filter',
        'uses' => \Kabooodle\Http\Controllers\Api\Sales\SalesFilterController::class.'@search'
    ]);
    $api->post('shipping/filter', [
        'as' => 'shipping.filter',
        'uses' => \Kabooodle\Http\Controllers\Api\Shipping\ShippingFilterController::class.'@search'
    ]);

    $api->resource('claims', \Kabooodle\Http\Controllers\Api\Claims\ClaimsApiController::class);
    $api->post('claims/{claims}/toggle_shipping', [
        'as' => 'claims.toggle',
        'uses' => \Kabooodle\Http\Controllers\Api\Claims\ClaimsApiController::class.'@switchShippingMethod'
    ]);

    $api->resource('groups', \Kabooodle\Http\Controllers\Api\Groups\GroupsApiController::class);
    $api->resource('groups.followers', \Kabooodle\Http\Controllers\Api\Groups\GroupsFollowersApiController::class);

});
