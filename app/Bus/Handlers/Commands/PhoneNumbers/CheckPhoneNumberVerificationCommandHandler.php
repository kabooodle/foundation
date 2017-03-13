<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\PhoneNumbers;

use Carbon\Carbon;
use Kabooodle\Models\Traits\SMSableTrait;
use Kabooodle\Bus\Commands\PhoneNumbers\CheckPhoneNumberVerificationCommand;
use Kabooodle\Bus\Events\PhoneNumbers\PhoneNumberWasVerifiedSuccessfullyEvent;

/**
 * Class CheckPhoneNumberVerificationCommandHandler
 */
class CheckPhoneNumberVerificationCommandHandler
{
    use SMSableTrait;

    /**
     * @param CheckPhoneNumberVerificationCommand $command
     */
    public function handle(CheckPhoneNumberVerificationCommand $command)
    {
        $phoneModel = $command->getPhoneNumberModel();
        $verificationCode = $command->getVerificationCode();
        $actor = $command->getActor();

        $client = $this->getNexmoClient();

        $client->verify()->check($phoneModel->token, $verificationCode);

        $phoneModel->verified = 1;
        $phoneModel->verified_on = Carbon::now();
        $phoneModel->save();

        event(new PhoneNumberWasVerifiedSuccessfullyEvent($phoneModel, $actor));
    }
}
