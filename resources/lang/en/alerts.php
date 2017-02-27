<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

return [
    'error_generic_retry' => 'An error occurred, please try again',
    'success_save' => 'Save successful',


    'listings' => [
        'listings_exceeds_hourly_limit' => 'You can only list :allowed per hour. You already have :current scheduled to be listed (or deleted) for the selected time period. Please try a choose a different time or remove some items.',
        'success_listing_fb_deleted' => 'Listing successfully queued for deletion.  All pending claims will be removed.',
        'facebook_token_invalid' => 'You need to connect (or reconnect) your ' . env('APP_NAME') . ' account to Facebook.',
        'success_customlisting_save' => 'Save successful, you can now use and share the link you created!',
        'success_listing_deleted' => 'Listing successfully deleted.  All items, and pending claims will also be removed.'
    ],
];


