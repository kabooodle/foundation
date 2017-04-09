<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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

            $shipmentStatus = $shippingTransaction->getLatestHistory();

            $this->subject = 'Shipping tracking status changed to ' . $shipmentStatus . ', for your purchase: ' . $claim->inventoryItem->name_with_variant;

            // Notifications for recipient
            if ($recipient->primaryEmail && $recipient->primaryEmail->isVerified()) {
                $this->toEmail($recipient->primaryEmail->address, $claim, $shippingTransaction, $shippingHistory);
            }

            $this->toDatabase($recipient, $claim, $shippingTransaction, $shippingHistory);

//            if ($shipmentStatus == 'DELIVERED'){
//                // Notify owner that a package was delivered;
//                $subject = $recipient->username.' purchase: '.$claim->inventoryItem->name_with_variant.' was DELIVERED!';
//                $this->toOwnerDatabase($shippingTransaction->user, $claim, $shippingTransaction, $subject);
//            }
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
        $notification->reference_url = route('profile.claims.show', [$claim->getUUID()]);
        $notification->payload = '';
        $notification->title = $this->subject;
        $notification->description = '';
        $notification->save();
    }

    /**
     * Disabled for now.
     * 
     * @param User                 $owner
     * @param Claims               $claim
     * @param ShippingTransactions $shipment
     * @param string               $subject
     */
    public function toOwnerDatabase(User $owner, Claims $claim, ShippingTransactions $shipment, string $subject)
    {
        $notification = new NotificationNotices;
        $notification->user_id = $owner->id;
        $notification->notification_id = null;
        $notification->reference_id = $claim->id;
        $notification->reference_type = get_class($claim);
        $notification->reference_url = route('merchant.shipping.transactions.show', [$shipment->shipping_shipments_uuid, $shipment->uuid]);
        $notification->payload = '';
        $notification->title = $subject;
        $notification->description = '';
        $notification->save();
    }
}
