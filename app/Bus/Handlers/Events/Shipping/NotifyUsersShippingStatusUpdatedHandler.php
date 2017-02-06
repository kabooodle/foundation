<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Shipping;

use Bugsnag;
use Kabooodle\Models\User;
use Kabooodle\Models\Claims;
use Kabooodle\Libraries\Emails\KitEmail;
use Kabooodle\Models\NotificationNotices;
use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Models\ShippingTransactionHistory;
use Kabooodle\Bus\Events\Shipping\ShippingTransactionStatusUpdatedEvent;

/**
 * Class NotifyUsersShippingStatusUpdatedHandler
 */
class NotifyUsersShippingStatusUpdatedHandler
{
    /**
     * @var string
     */
    public $subject;

    /**
     * @param ShippingTransactionStatusUpdatedEvent $event
     */
    public function handle(ShippingTransactionStatusUpdatedEvent $event)
    {
        try {
            $shippingTransaction = ShippingTransactions::where('id', '=', $event->getShippingTransactionId())
                ->firstOrFail();

            /** @var ShippingTransactionHistory $shippingHistory */
            $shippingHistory = $shippingTransaction->shippingHistory->find($event->getShippingTransactionHistoryId());

            /** @var User $recipient */
            $recipient = $shippingTransaction->recipient;

            /** @var Claims $claim */
            $claim = $shippingTransaction->shipment->claim();

            $this->subject = 'Shipping tracking status changed to ' . $shippingTransaction->getLatestHistory() . ', for your purchase: ' . $claim->inventoryItem->name_with_variant;

            if ($recipient->primaryEmail && $recipient->primaryEmail->isVerified()) {
                $this->toEmail($recipient->primaryEmail->address, $claim, $shippingTransaction, $shippingHistory);
            }

            $this->toDatabase($recipient, $claim, $shippingTransaction, $shippingHistory);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }

    /**
     * @param string                     $emailAddress
     * @param Claims                     $claim
     */
    public function toEmail(string $emailAddress, Claims $claim)
    {
        $subject = $this->subject;

        $email = new KitEmail;
        $email->setView('shipping.emails.shipment_tracking_updated')
            ->setCallable(function ($m) use ($emailAddress, $subject) {
                $m->to($emailAddress)
                    ->subject($subject);
            })
            ->setParameters([
                'claim' => $claim,
                'subject' => $subject
            ])
            ->send();
    }

    /**
     * @param User   $recipient
     * @param Claims $claim
     */
    public function toDatabase(User $recipient, Claims $claim)
    {
        $notification = new NotificationNotices;
        $notification->user_id = $recipient->id;
        $notification->notification_id = null;
        $notification->reference_id = $claim->id;
        $notification->reference_type = get_class($claim);
        $notification->reference_url = route('profile.purchases.show', [$claim->getUUID()]);
        $notification->payload = '';
        $notification->title = $this->subject;
        $notification->description = '';
        $notification->save();
    }
}
