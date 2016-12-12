<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Email\AddEmailCommand;
use Kabooodle\Bus\Events\Email\EmailWasCreatedEvent;
use Kabooodle\Models\Email;

/**
 * Class AddEmailCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class AddEmailCommandHandler
{
    use DispatchesJobs;

    protected $email;

    /**
     * AddEmailCommandHandler constructor.
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @param AddEmailCommand $command
     *
     * @return Email
     */
    public function handle(AddEmailCommand $command)
    {
        $email = $this->email;
        $email = $email::factory([
            'user_id' => $command->getUser()->id,
            'address' => $command->getAddress(),
            'primary' => $command->isPrimary(),
        ]);

        if ($email->isPrimary()) {
            $previousEmails = $this->email->whereUserId($command->getUser()->id)->where('id', '!=', $email->id)->get();
            foreach ($previousEmails as $previousEmail) {
                $previousEmail->primary = false;
                $previousEmail->save();
            }
        }

        event(new EmailWasCreatedEvent($email));

        return $email;
    }
}
