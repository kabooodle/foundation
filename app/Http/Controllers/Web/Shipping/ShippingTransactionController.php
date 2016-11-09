<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
            $shippingTransaction = $this->dispatchNow(new CreateNewShippingTransactionCommand(user(), $rateUUID, $shipmentUUID));
            $redirectRoute = route('shipping.transactions.show', [$shipmentUUID, $shippingTransaction->uuid]);

            return Response::json(['txn_id' => $shippingTransaction->transaction_id, 'redirect' => $redirectRoute], 200);
        } catch (InsufficientBalanceException $e) {
            return Response::json(['error' => 'Insufficient credits : $'.user()->getAvailableBalance()], 500);
        } catch (ShippoException $e){
            return Response::json(['error' => $e->getMessage()], 500);
        } catch (StaleDataException $e) {
            return Response::json(['error' => 'Try again'], 500);
        } catch (Exception $e) {
            return Response::json(['error' => 'Try again'], 500);
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
        $transaction = $this->dispatchNow(new GetShippingTransactionCommand(user(),$shipmentUUID, $transactionUUID));

        return $this->view('shipping.order.transaction')->with(compact('transaction'));
    }

    /**
     * @param Request $request
     * @param $shipmentUUID
     * @param $transactionUUID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function label(Request $request, $shipmentUUID, $transactionUUID)
    {
        $transaction = $this->dispatchNow(new GetShippingTransactionCommand(user(),$shipmentUUID, $transactionUUID));

        event(new ShippingLabelPrinted($transaction));

        return $this->redirect()->to($transaction->label_url);
    }
}