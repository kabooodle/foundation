<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\PhoneNumbers;

use Bugsnag;
use Exception;
use Kabooodle\Models\Traits\SMSableTrait;
use Kabooodle\Bus\Events\PhoneNumbers\PhoneNumberWasVerifiedSuccessfullyEvent;

/**
 * Class NotifyNewPhoneNumberVerified
 */
class NotifyNewPhoneNumberVerified
{
    use SMSableTrait;

    /**
     * @param PhoneNumberWasVerifiedSuccessfullyEvent $event
     */
    public function handle(PhoneNumberWasVerifiedSuccessfullyEvent $event)
    {
        $actor = $event->getActor();
        $phoneNumberModel = $event->getPhoneNumberModel();
        try {
            $client = $this->getNexmoClient();
            $client->message()->send([
                'to' => $phoneNumberModel->number,
                'from' => env('NEXMO_FROM'),
                'text' => 'Your phone number was verified successfully! Thank you! - kabooodle'
            ]);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}