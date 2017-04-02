<?php

require 'merchant_routes.php';

Route::get('c/{hash}', [
    'as' => 'externalclaim.show',
    'uses' => \Kabooodle\Http\Controllers\Web\Claims\ClaimingController::class.'@show'
]);

Route::get('c/s/{sellableType}/{hash}', [
    'as' => 'externalclaim.shoppable.show',
    'uses' => \Kabooodle\Http\Controllers\Web\Listings\ListingsController::class.'@customLink'
]);

Route::get('claims/verify/{token}', [
    'as' => 'claims.verify',
    'uses' => \Kabooodle\Http\Controllers\Web\Claims\ClaimsController::class.'@verifyClaim'
]);

Route::get('claims/verified', [
    'as' => 'claims.verified',
    'uses' => \Kabooodle\Http\Controllers\Web\Claims\ClaimsController::class.'@verifiedClaim'
]);

Route::get('claims/verification-failure', [
    'as' => 'claims.verification-failure',
    'uses' => \Kabooodle\Http\Controllers\Web\Claims\ClaimsController::class.'@claimVerificationFail'
]);
