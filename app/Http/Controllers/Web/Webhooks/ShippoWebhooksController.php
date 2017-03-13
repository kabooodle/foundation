<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Webhooks;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Http\Controllers\Web\Controller;
use Symfony\Component\HttpFoundation\Response;
use Kabooodle\Models\ShippingTransactionHistory;
use Kabooodle\Bus\Events\Shipping\ShippingTransactionStatusUpdatedEvent;

/**
 * Class ShippoWebhooksController
 */
class ShippoWebhooksController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleWebhook(Request $request)
    {
        // Yah, uber topsecret webhook token o.O
        if (!$request->has('x') || $request->get('x') <> 'KuMQnR5hhzlM2Wk7q9aS') {
            return new Response('', 401);
        }

        $payload = json_decode($request->getContent(), true);

        // Store some reusable variables!
        $transactionId = $this->getTransactionId($payload);
        $trackingNumber = $this->getTrackingNumber($payload);

        // Array containing latest tracking information.
        $trackingStatus = $payload['tracking_status'];

        // 'UNKNOWN', 'DELIVERED', 'TRANSIT', 'FAILURE', 'RETURNED'.
        $status = $trackingStatus['status'];
        $mappedStatus = ShippingTransactions::mapShippoStatiiToLocalStatii($status);

        // UTC date 2017-02-06T05:46:06.000Z
        $statusDate = Carbon::createFromFormat('Y-m-d\TH:i:s.000\Z', $trackingStatus['status_date']);

        // string
        $statusDetails = $trackingStatus['status_details'];

        // {city, state, zip, country}
        $statusLocation = $trackingStatus['location'];

        // Array of objects
        $trackingHistory = $payload['tracking_history'];

        // Check if the incoming payload references a shipping transaction on our end.
        // I'm not sure why it will never be a 1 to 1 relationship, but for now its just incase.

        /** @var ShippingTransactions|null $transaction */
        $transaction = $this->findShippingTransaction($transactionId, $trackingNumber);

        // We log all webhooks even if there is no matching transaction.
        $history = ShippingTransactionHistory::create([
            'shipping_transaction_id' => $transaction? $transaction->id : null,
            'payload' => $payload,
            'status' => $status,
            'status_details' => $statusDetails,
            'status_date' => $statusDate,
            'status_location' => $statusLocation,
            'tracking_history' => $trackingHistory
        ]);


        // We have an existing transaction that matches the update
        if ($transaction) {
            $transaction->shippingHistory()->save($history);
            $transaction->tracking_history = $payload['tracking_history'];
            $transaction->shipping_status_updated_on = $statusDate;
            $transaction->shipping_status = $mappedStatus;
            $transaction->save();

            // Tell all those pesky listeners that a new event, related to a transaction was updated!
            event(new ShippingTransactionStatusUpdatedEvent($transaction->id, $history->id));
        }

        return new Response('Ok', 200);
    }

    /**
     * @param $transactionId
     * @param $trackingNumber
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function findShippingTransaction($transactionId, $trackingNumber)
    {
        return ShippingTransactions::where('transaction_id', $transactionId)
            ->where('tracking_number', $trackingNumber)
            ->first();
    }

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function getTrackingNumber($payload)
    {
        return $payload['tracking_number'];
    }

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function getTransactionId($payload)
    {
        return $payload['object_id'];
    }

    /**
     * @param $payload
     *
     * @return bool
     */
    public function successStatus($payload)
    {
        return $payload['object_status'] == 'SUCCESS';
    }
}
