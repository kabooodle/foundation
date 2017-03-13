<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Shipping;

use Binput;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Foundatino\Exceptions\StaleDataException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Foundation\Exceptions\Shippo\ShippoException;
use Kabooodle\Bus\Commands\Shipping\CreateNewShippingTransactionCommand;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;

/**
 * Class ShippingLabelController
 */
class ShippingLabelController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, ['rate_uuid' => 'required', 'parcel_id' => 'required|int']);

            $job = new CreateNewShippingTransactionCommand(user(), Binput::get('rate_uuid'), Binput::get('parcel_id'));

            $shippingTransaction = $this->dispatchNow($job);
            $redirectRoute = route('merchant.shipping.transactions.show', [$shippingTransaction->shipping_shipments_uuid, $shippingTransaction->uuid]);

            return $this->setData([
                'msg' => 'Shipping label purchased successfully. Redirecting you to the details page.',
                'redirect' => $redirectRoute,
                'txn_id' => $shippingTransaction->transaction_id
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setData([
                'msg' => $e->validator->getMessageBag()->first()
            ])->setStatusCode(400)->respond();
        } catch (InsufficientBalanceException $e) {
            return $this->setData([
                'msg' => 'Insufficient credits : $'.user()->getAvailableBalance()
            ])->setStatusCode(500)->respond();
        } catch (ShippoException $e) {
            return $this->setData([
                'msg' => 'An error occurred, please try again'
            ])->setStatusCode(500)->respond();
        } catch (StaleDataException $e) {
            return $this->setData([
                'msg' => 'An error occurred, please try again'
            ])->setStatusCode(500)->respond();
        } catch (Exception $e) {
            return $this->setData([
                'msg' => 'An error occurred, please try again'
            ])->setStatusCode(500)->respond();
        }
    }
}
