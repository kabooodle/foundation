<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Subscription;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Models\NotificationNotices;
use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\Subscriptions\InvoicePaymentFailed;

/**
 * Class NotifyUserInvoicePaymentFailed
 */
class NotifyUserInvoicePaymentFailed
{
    /**
     * @var string
     */
    protected $subject = 'Invoice payment failed';

    /**
     * @param InvoicePaymentFailed $event
     */
    public function handle(InvoicePaymentFailed $event)
    {
        $user = $event->getUser();
        $invoiceTotal = $event->getInvoice()->total();
        try {
            $this->toEmail($user, $user->primaryEmail->address, $invoiceTotal);
            $this->toDatabase($user);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param User   $user
     * @param string $emailAddress
     * @param        $invoiceTotal
     */
    public function toEmail(User $user, string $emailAddress, $invoiceTotal)
    {
        $subject = $this->subject;
        $email = new PiperEmail;
        $email->setView('subscription.emails.paymentfailed')
            ->setParameters([
                'user' => $user,
                'total' => $invoiceTotal
            ])
            ->setCallable(function ($m) use ($emailAddress, $subject) {
                $m->to($emailAddress)
                    ->subject($subject);
            })
            ->send();
    }

    /**
     * @param User $user
     */
    public function toDatabase(User $user)
    {
        $notification = new NotificationNotices;
        $notification->user_id = $user->id;
        $notification->notification_id = null;
        $notification->reference_id = '';
        $notification->reference_type = '';
        $notification->reference_url = route('profile.creditcard.index');
        $notification->payload = '';
        $notification->title = $this->subject;
        $notification->description = '';
        $notification->save();
    }
}
