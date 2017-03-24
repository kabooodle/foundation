<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Emails;

use Kabooodle\Models\Claims;
use Kabooodle\Models\Email;

/**
 * Class PiperEmail
 * @package Kabooodle\Libraries\Emails
 */
class PiperEmail extends AbstractEmail
{
    /**
     * @return string
     */
    public function getEmailTemplate()
    {
        return 'emails.templates.base';
    }

    /**
     * @param Email $email
     */
    public function sendEmailVerificationEmail(Email $email)
    {
        $this->setView('emails.verification.email')
            ->setParameters([
                'email' => $email,
                'user' => $email->user,
                'verifyLink' => route('emails.verify', $email->token),
            ])
            ->setCallable(function ($m) use ($email) {
                $m->to($email->address)
                    ->subject('Verify your '.env('APP_NAME').' email address');
            })
            ->send();
    }

    /**
     * @param Claims $claim
     * @param Email $email
     */
    public function sendClaimVerificationEmails(Claims $claim, Email $email)
    {
        // Send Guest email
        $this->setView('emails.verification.claim')
            ->setParameters([
                'claim' => $claim,
                'user' => $email->user,
                'verifyLink' => route('claims.verify', $claim->token),
            ])
            ->setCallable(function ($m) use ($email) {
                $m->to($email->address)
                    ->subject('Verify your '.env('APP_NAME').' claim');
            })
            ->send();

        // Send Seller email
        $this->setView('emails.claims.pending-verification')
            ->setParameters([
                'claim' => $claim,
                'item' => $claim->listedItem,
                'user' => $claim->listedItem->owner,
                'itemLink' => null,
                'claimsLink' => null,
            ])
            ->setCallable(function ($m) use ($claim) {
                $m->to($claim->listedItem->owner->primaryEmail->address)
                    ->subject('A new claim pending verification on '.env('APP_NAME'));
            })
            ->send();
    }
}
