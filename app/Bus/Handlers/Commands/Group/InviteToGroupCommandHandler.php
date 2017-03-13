<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Group;

use Kabooodle\Models\User;
use Illuminate\Contracts\Mail\MailQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Group\InviteToGroupCommand;

/**
 * Class InviteToGroupCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Group
 */
class InviteToGroupCommandHandler
{
    use DispatchesJobs;

    /**
     * InviteToGroupCommandHandler constructor.
     *
     * @param MailQueue $mailer
     */
    public function __construct(MailQueue $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param InviteToGroupCommand $job
     */
    public function handle(InviteToGroupCommand $job)
    {
        $email = $job->getEmail();

        $subject = 'you were invited to a group!';

        // Check if the user already exists in our platform
        if ($user = $this->userEmailExists($email)) {
            $this->mailer->queue('groups.emails.invited', ['user' => $user], function ($m) use ($user, $subject) {
                $m->to($user->email)->subject($subject);
            });
        } else {
            $this->mailer->queue('groups.emails.invited', ['user' => $email], function ($m) use ($email, $subject) {
                $m->to($email)->subject($subject);
            });
        }
    }

    /**
     * @param $email
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function userEmailExists($email)
    {
        return User::where('email', $this->cleanEmailAddress($email))->first();
    }

    /**
     * @param $email
     *
     * @return string
     */
    protected function cleanEmailAddress($email)
    {
        return trim(strtolower($email));
    }
}
