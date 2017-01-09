<?php

$api->post('/queues/errors', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@errorQueueHandler');

$api->post('/queues/general', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueGeneralHandler');
$api->post('/queues/email', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueEmailHandler');

$api->post('/queues/viewtracker', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueViewTrackerHandler');
$api->post('/queues/viewtracker-b', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueViewTrackerBHandler');


$api->post('/queues/fb-scheduler', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleHandler');
$api->post('/queues/fb-scheduler-b', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleBHandler');
$api->post('/queues/fb-scheduler-c', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleCHandler');
$api->post('/queues/fb-scheduler-d', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleDHandler');
$api->post('/queues/fb-scheduler-e', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleEHandler');


$api->post('/queues/fb-lister', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingHandler');
$api->post('/queues/fb-lister-b', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingBHandler');
$api->post('/queues/fb-lister-c', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingCHandler');
$api->post('/queues/fb-lister-d', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingDHandler');
$api->post('/queues/fb-lister-e', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingEHandler');
$api->post('/queues/fb-lister-f', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingFHandler');
$api->post('/queues/fb-lister-g', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingGHandler');
$api->post('/queues/fb-lister-h', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingHHandler');
$api->post('/queues/fb-lister-i', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingIHandler');
$api->post('/queues/fb-lister-j', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookListingJHandler');