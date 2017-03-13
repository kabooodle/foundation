<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Shipping;

use DB;
use Kabooodle\Models\ShippingQueue;
use Kabooodle\Models\ShippingShipments;
use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Services\Shippr\RatesObject;
use Kabooodle\Services\Shippr\ShipprService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Foundation\Exceptions\Shippo\ShippoException;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Bus\Commands\Credits\DebitUserCreditBalanceCommand;
use Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent;
use Kabooodle\Bus\Commands\Shipping\CreateNewShippingTransactionCommand;

/**
 * Class CreateNewShippingTransactionCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Shipping
 */
class CreateNewShippingTransactionCommandHandler
{
    use DispatchesJobs;

    /**
     * @param CreateNewShippingTransactionCommand $command
     * @return mixed
     */
    public function handle(CreateNewShippingTransactionCommand $command)
    {
        $parcelId = $command->getParcelId();
        $rateUUID = $command->getRateUUID();
        $user = $command->getActor();

        $shipment = ShippingShipments::where('id', $parcelId)->where('user_id', $user->id)->firstOrFail();
        $claimer = $shipment->claimer;

        /** @var RatesObject $rate */
        $rate = $shipment->getRateId($rateUUID);

        /** @var ShippingTransactions $transaction */
        return DB::transaction(function () use ($rate, $shipment, $parcelId, $user, $rateUUID, $claimer) {
            $shippr = new ShipprService;

            // Debit a shipping transaction with shippo.
            $transaction = $shippr->createLabel($rateUUID);

            // If an error is returned from shippo, throw an exception.
            if ($transaction['object_status'] == 'ERROR' || in_array($transaction['object_state'], ['INVALID', 'INCOMPLETE'])) {
                throw new ShippoException($transaction['messages'][0]['text']);
            }

            // Debit the user's balance.
            $this->dispatchNow(new DebitUserCreditBalanceCommand(
                $user,
                $rate->getAdjustedTotalAmount(),
                CreditTransactableInterface::TYPE_DEBIT
            ));

            // Remove associated claims from shipping queue
            $this->removeClaimsFromShippingQueue($shipment);

            // Log the shipping transaction.
            $st = new ShippingTransactions;
            $st->user_id = $user->id;
            $st->recipient_id = $claimer->id;
            $st->shipping_shipments_id = $shipment->id;
            $st->shipping_shipments_uuid = $shipment->uuid;
            $st->raw_response = $transaction->__toArray(true);
            $st->transaction_id = $transaction['object_id'];
            $st->rate_id = $rateUUID;
            $st->label_url = $transaction['label_url'];
            $st->rate_data = $rate;
            $st->rate_amount = $rate->getAmount();
            $st->rate_final_amount = $rate->getAdjustedTotalAmount();
            $st->tracking_number = $transaction['tracking_number'];
            $st->tracking_status = $transaction['tracking_status'];
            $st->tracking_url_provider = $transaction['tracking_url_provider'];
            $st->tracking_history = $transaction['tracking_history'];
            $st->status = $transaction['object_status'];
            $st->messages = $transaction['messages'];
            $st->save();

            event(new ShippingTransactionWasCreatedEvent($st));

            return $st;
        });
    }

    /**
     * @param ShippingShipments $shipment
     *
     * @return void
     */
    public function removeClaimsFromShippingQueue(ShippingShipments $shipment)
    {
        $claimIds = $shipment->claims->pluck('id');
        ShippingQueue::whereIn('claim_id', $claimIds)->delete();
    }
}
