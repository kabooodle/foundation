<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Webhooks;

use Kabooodle\Bus\Events\User\UserSubscriptionCameOffTrial;
use Kabooodle\Models\User;
use Laravel\Cashier\Http\Controllers\WebhookController;

/**
 * Class StripeWebhooksController
 * @package Kabooodle\Http\Controllers\Web\Webhooks
 */
class StripeWebhooksController extends WebhookController
{
    public function handleCustomerSubscriptionUpdated(array $payload)
    {
        $user = User::where(($payload['data']['object']['customer']), 'stripe_id')->first();
        if($user) {
            if($payload['data']['previous_attributes']['status'] == "trialing" && $payload['data']['object']['status'] == "active") {
                event(new UserSubscriptionCameOffTrial($user, $payload));
                dd('fart');
            }
        }
    }
}
