<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Libraries\Emails;
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

    public function sendVerificationEmail(Email $email)
    {
//        $mail = new PiperEmail;
//        $mail->setView('email.verification')
//            ->setParameters(['email' => $email])
//            ->setCallable(function ($m) use ($email) {
//                $m->to($email->email)
//                    ->subject('Welcome to '.env('APP_NAME').'!');
//            })
//            ->send();
    }
}
