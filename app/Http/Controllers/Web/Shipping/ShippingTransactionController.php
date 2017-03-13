<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shipping;

use Binput;
use Response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Events\Shipping\ShippingLabelPrinted;
use Kabooodle\Foundatino\Exceptions\StaleDataException;
use Kabooodle\Foundation\Exceptions\Shippo\ShippoException;
use Kabooodle\Bus\Commands\Shipping\GetShippingTransactionCommand;
use Kabooodle\Bus\Commands\Shipping\CreateNewShippingTransactionCommand;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;

/**
 * Class ShippingTransactionController
 * @package Kabooodle\Http\Controllers\Web\Shipping
 */
class ShippingTransactionController extends Controller
{
    use DispatchesJobs;

    /**
     * @param Request $request
     * @param $shipmentUUID
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $shipmentUUID)
    {
        // Check if this already has a shipping label generated. If so, redirect to it.
        $rateUUID = Binput::get('rate');

        try {
            $shippingTransaction = $this->dispatchNow(new CreateNewShippingTransactionCommand(webUser(), $rateUUID, $shipmentUUID));
            $redirectRoute = route('merchant.shipping.transactions.show', [$shipmentUUID, $shippingTransaction->uuid]);

            return Response::json(['txn_id' => $shippingTransaction->transaction_id, 'redirect' => $redirectRoute], 200);
        } catch (InsufficientBalanceException $e) {
            return Response::json(['error' => 'Insufficient credits : $'.webUser()->getAvailableBalance()], 500);
        } catch (ShippoException $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        } catch (StaleDataException $e) {
            return Response::json(['error' => 'Try again'], 500);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @param $shipmentUUID
     * @param $transactionUUID
     * @return $this
     */
    public function show(Request $request, $shipmentUUID, $transactionUUID)
    {
        $transaction = $this->dispatchNow(new GetShippingTransactionCommand(webUser(), $shipmentUUID, $transactionUUID));

        return $this->view('shipping.order.transaction')->with(compact('transaction'));
    }

    /**
     * @param Request $request
     * @param         $shipmentUUID
     * @param         $transactionUUID
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function label(Request $request, $shipmentUUID, $transactionUUID)
    {
        $transaction = $this->dispatchNow(new GetShippingTransactionCommand(webUser(), $shipmentUUID, $transactionUUID));

        event(new ShippingLabelPrinted($transaction));

        $remoteUrl = $transaction->label_url;
        $filename = 'label.pdf';
        $tempImage = tempnam(storage_path('tmp/'), $filename);
        copy($remoteUrl, $tempImage);

        return response()->download($tempImage, $filename);
    }
}
