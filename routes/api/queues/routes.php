<?php

$api->post('/queues/errors', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@errorQueueHandler');

$api->post('/queues/general', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueGeneralHandler');

// Email Queue
$api->post('/queues/email', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueEmailHandler');
$api->post('/queues/email-b', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueEmailBHandler');
$api->post('/queues/email-c', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueEmailCHandler');
$api->post('/queues/email-d', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueEmailDHandler');
$api->post('/queues/email-e', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueEmailEHandler');
$api->post('/queues/email-f', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueEmailFHandler');
$api->post('/queues/email-g', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueEmailGHandler');

// Views Tracker Queue
$api->post('/queues/viewtracker', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueViewTrackerHandler');
$api->post('/queues/viewtracker-b', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueViewTrackerBHandler');

// Schedule Queue
$api->post('/queues/fb-scheduler', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleHandler');
$api->post('/queues/fb-scheduler-b', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleBHandler');
$api->post('/queues/fb-scheduler-c', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleCHandler');
$api->post('/queues/fb-scheduler-d', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleDHandler');
$api->post('/queues/fb-scheduler-e', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookScheduleEHandler');

// Delete Queue
$api->post('/queues/fb-deleter', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookDeleterHandler');
$api->post('/queues/fb-deleter-b', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookDeleterBHandler');
$api->post('/queues/fb-deleter-c', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookDeleterCHandler');
$api->post('/queues/fb-deleter-d', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookDeleterDHandler');
$api->post('/queues/fb-deleter-e', \Kabooodle\Http\Controllers\Api\Queues\PushQueueController::class.'@queueFacebookDeleterEHandler');

// Listing Queue
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