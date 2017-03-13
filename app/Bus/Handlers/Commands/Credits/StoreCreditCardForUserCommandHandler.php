<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Credits;

use Stripe\Token;
use Kabooodle\Bus\Commands\Credits\StoreCreditCardForUserCommand;

/**
 * Class StoreCreditCardForUserCommand
 * @package Kabooodle\Bus\Commands\Profile
 */
class StoreCreditCardForUserCommandHandler
{
    /**
     * @param StoreCreditCardForUserCommand $command
     */
    public function handle(StoreCreditCardForUserCommand $command)
    {
        $actor = $command->getActor();

        if (!$actor->hasStripeId()) {
            $token = Token::create([
                'card[exp_month]' => $command->getExpMo(),
                'card[exp_year]' => $command->getExpYr(),
                'card[number]' => $command->getCardNumber(),
                'card[currency]' => 'usd',
                'card[cvc]' => $command->getCvv()
            ], ['api_key' => env('STRIPE_SECRET')]);

            $actor->createAsStripeCustomer($token->id);
        }

        $actor->asStripeCustomer()->sources->create([
            'source' => [
                'object' => 'card',
                'exp_month' => $command->getExpMo(),
                'exp_year' => $command->getExpYr(),
                'number' => $command->getCardNumber(),
                'currency' => 'usd',
                'cvc' => $command->getCvv()
            ]
        ]);
    }
}
