<?php

namespace Kabooodle\Tests;

use Stripe\Token;

/**
 * Class InteractsWithStripeTrait
 */
trait InteractsWithStripeTrait
{
    /**
     * Get a test Stripe token.
     *
     * @return string
     */
    protected function getStripeToken()
    {
        return Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => 2020,
                'cvc' => "123",
            ],
        ], config('services.stripe.secret'))->id;
    }
}
