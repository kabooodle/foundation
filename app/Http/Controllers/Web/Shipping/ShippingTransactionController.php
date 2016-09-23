<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shipping;

use Binput;
use Illuminate\Http\Request;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;
use Kabooodle\Models\ShippingShipments;
use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Services\Shippr\ShipprService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent;

/**
 * Class ShippingTransactionController
 * @package Kabooodle\Http\Controllers\Web\Shipping
 */
class ShippingTransactionController extends Controller
{
    use DispatchesJobs;

    /**
     * @param Request $request
     * @param         $shipmentUUID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $shipmentUUID)
    {
        // Check if this already has a shipping label generated. If so, redirect to it.
        $rateUUID = Binput::get('rate');
        $shipment = ShippingShipments::where('uuid', $shipmentUUID)->where('user_id', user()->id)->firstOrFail();
        $rate = $shipment->getRateId($rateUUID);

//        if (! user()->hasSufficientBalance($rate->getAmount())) {
//            return \Response::json(['error' => 'Insufficient credits : $'.user()->getAvailableBalance()], 500);
//        }

        try {
            $shippr = new ShipprService;

            $transaction = $shippr->createLabel($rateUUID);

            $st = new ShippingTransactions;
            $st->user_id = user()->id;
            $st->shipping_shipments_id = $shipment->id;
            $st->shipping_shipments_uuid = $shipment->uuid;
            $st->raw_response = $transaction->__toArray(true);
            $st->transaction_id = $transaction['object_id'];
            $st->rate_id = $rateUUID;
            $st->label_url = $transaction['label_url'];
            $st->rate_data = $rate;
            $st->rate_amount = $rate->getAmount();
            $st->tracking_number = $transaction['tracking_number'];
            $st->tracking_status = $transaction['tracking_status'];
            $st->tracking_url_provider = $transaction['tracking_url_provider'];
            $st->tracking_history = $transaction['tracking_history'];
            $st->status = $transaction['object_status'];
            $st->messages = $transaction['messages'];

            if (!$st->save()) {
                throw new InsufficientBalanceException;
            }

            $this->dispatch(new ShippingTransactionWasCreatedEvent($st));

            $redirectRoute = route('shipping.transactions.show', [$shipmentUUID, $st->uuid]);

            return \Response::json(['txn_id' => $transaction['object_id'], 'redirect' => $redirectRoute], 200);

        } catch (InsufficientBalanceException $e) {
            return \Response::json(['error' => 'Insufficient credits : $'.user()->getAvailableBalance()], 500);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * @param Request $request
     * @param         $shipmentUUID
     * @param         $transactionUUID
     *
     * @return $this
     */
    public function show(Request $request, $shipmentUUID, $transactionUUID)
    {
        $shipment = ShippingShipments::where('uuid', $shipmentUUID)->where('user_id', user()->id)->firstOrFail();
        $transaction = ShippingTransactions::where('uuid', $transactionUUID)->where('user_id', user()->id)->firstOrFail();

        return $this->view('shipping.order.transaction')->with(compact('transaction', 'shipment'));
    }
}