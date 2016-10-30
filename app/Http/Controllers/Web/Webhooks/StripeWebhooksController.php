<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Webhooks;

use Kabooodle\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Kabooodle\Bus\Events\User\UserSubscriptionCameOffTrial;

/**
 * Class StripeWebhooksController
 * @package Kabooodle\Http\Controllers\Web\Webhooks
 */
class StripeWebhooksController extends WebhookController
{
    /**
     * @param array $payload
     *
     * @return Response
     */
    public function handleCustomerSubscriptionUpdated(array $payload)
    {
        /** @var User $user */
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        // The major thing we care about is the fact that a user is no longer in trial
        // and their account has rolled over to the first pay status.
        // This can be confirmed be checking their previous status and current!
        if($user
            && $payload['data']['previous_attributes']['status'] == "trialing"
            && $payload['data']['object']['status'] == "active") {

            // We have an account that just got off trial, fire the event.
            event(new UserSubscriptionCameOffTrial($user, $payload));
        }

        return new Response('Webhook Handled', 200);
    }
}
