<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\PhoneNumbers;

use Kabooodle\Models\PhoneNumber;
use Kabooodle\Models\Traits\SMSableTrait;
use Nexmo\Client\Exception\Exception as NexmoException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Bus\Commands\PhoneNumbers\StartNewVerificationCommand;
use Kabooodle\Bus\Events\PhoneNumbers\NewPhoneNumberVerificationStarted;

/**
 * Class StartNewVerificationCommandHandler
 */
class StartNewVerificationCommandHandler
{
    use SMSableTrait;

    /**
     * @param StartNewVerificationCommand $command
     *
     * @return \Nexmo\Verify\Verification
     * @throws NexmoException
     */
    public function handle(StartNewVerificationCommand $command)
    {
        $actor = $command->getActor();
        $phoneNumber = $command->getPhoneNumber();

        try {
            $client = $this->getNexmoClient();
            $verification = $client->verify()->start([
                'number' => $phoneNumber,
                'brand'  => env('NEXMO_BRAND')
            ]);
        } catch (NexmoException $e) {
            // The only reason we are using try catch is because there is a specific type of excception code
            // we want to listen for.  "10" indicates the user is requesting a new verification code for
            // the same phone number.  We want to "retrigger" a new code in this case. For everything else
            // just throw the exception.
            switch ($e->getCode()) {
                case 10:
                    $model = PhoneNumber::where('user_id', $actor->id)
                        ->where('number', $phoneNumber)
                        ->latest()
                        ->first();

                    if (! $model) {
                        throw new ModelNotFoundException('An error occurred, please wait 5 minutes and try again.');
                    }

                    $verification = $client->verify()->trigger($model->token);
                    $model->token = $verification->getRequestId();
                    $model->save();

                    return true;
                    break;

                default:
                    throw $e;
                    break;
            }
        }

        $model = PhoneNumber::firstOrCreate([
            'user_id' => $actor->id,
            'number' => $phoneNumber,
        ]);
        $model->verified = false;
        $model->verified_on = null;
        $model->token = $verification->getRequestId();
        $model->save();

        event(new NewPhoneNumberVerificationStarted($model, $actor));

        return $verification;
    }
}
