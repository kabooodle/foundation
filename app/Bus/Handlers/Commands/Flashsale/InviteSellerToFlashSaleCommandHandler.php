<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use Kabooodle\Models\Invitations;
use Kabooodle\Models\User;
use Illuminate\Contracts\Mail\MailQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Flashsale\InviteSellerToFlashSaleCommand;

/**
 * Class InviteToGroupCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Group
 */
class InviteSellerToFlashSaleCommandHandler
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
     * @param InviteSellerToFlashSaleCommand $job
     */
    public function handle(InviteSellerToFlashSaleCommand $job)
    {
        $email = trim($job->getEmail());
        if (! $email) {
            return;
        }

        $subject = 'you were invited to participate in a flash sale as a seller!';

        $user = $this->userEmailExists($email);
        $invitations = $job->getFlashsale()->invitations;

        // Have we already invited this pleb?
        if ($invitations->count() > 0 && $user && $alreadyInvited = $invitations->filter(function ($inv) use ($email, $user) {
            if ($inv->user_id) {
                return $inv->user_id == $user->id;
            }
            return $inv->email == $email;
        })->first()) {
            return;
        }

        $invitation = new Invitations([
            'invited_by' => $job->getInvitedBy()->id,
            'email' => $email,
            'user_id' => $user ? $user->id : null
        ]);

        $job->getFlashsale()->invitations()->save($invitation);

        // Check if the user already exists in our platform
        if ($user) {
            $this->mailer->queue('flashsales.emails.invited_seller', ['user' => $user, 'invitation' => $invitation, 'flashsale' => $job->getFlashsale(), 'uuid' => $job->getFlashsale()->getUUID()], function ($m) use ($user, $subject) {
                $m->to($user->email)->subject($subject);
            });
        } else {
            $this->mailer->queue('flashsales.emails.invited_seller', ['user' => $email, 'invitation' => $invitation, 'flashsale' => $job->getFlashsale(), 'uuid' => $job->getFlashsale()->getUUID()], function ($m) use ($email, $subject) {
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
