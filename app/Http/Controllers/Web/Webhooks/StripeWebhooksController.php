<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Webhooks;

use Kabooodle\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Kabooodle\Bus\Events\User\UserSubscriptionCameOffTrial;
use Kabooodle\Bus\Events\Subscriptions\InvoicePaymentFailed;
use Kabooodle\Bus\Events\Subscriptions\SubscriptionCancelled;

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

        if ($user) {

            // The major thing we care about is the fact that a user is no longer in trial
            // and their account has rolled over to the first pay status.
            // This can be confirmed be checking their previous status and current!
           if ($this->checkIfUserCameOffTrialAndActive($payload)) {
               // We have an account that just got off trial, fire the event.
               event(new UserSubscriptionCameOffTrial($user, $payload));
           }
        }

        return new Response('Webhook Handled', 200);
    }

    /**
     * Handle a cancelled customer from a Stripe subscription.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $user->subscriptions->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            })->each(function ($subscription) use ($payload, $user) {
                $subscription->markAsCancelled();
                event(new SubscriptionCancelled($user, $subscription, $payload));
            });
        }

        return new Response('Webhook Handled', 200);
    }

    /**
     * @param array $payload
     *
     * @return Response
     */
    protected function handleInvoicePaymentFailed(array $payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user && $invoice = $user->findInvoice($payload['data']['object']['id'])) {
            event(new InvoicePaymentFailed($user, $invoice, $payload));
        }

        return new Response('Webhook Handled', 200);
    }

    /**
     * @param array $payload
     *
     * @return bool
     */
    protected function checkIfUserCameOffTrialAndActive(array $payload)
    {
        return
            isset($payload['data']['previous_attributes']['status']) && $payload['data']['previous_attributes']['status'] == "trialing"
            && $payload['data']['object']['status'] == "active";
    }
}
