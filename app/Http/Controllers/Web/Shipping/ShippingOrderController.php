<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shipping;

use Binput;
use Kabooodle\Foundation\Exceptions\Shippo\NoRatesFoundForParcelException;
use Messages;
use Shippo_Error;
use Illuminate\Http\Request;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Models\ShippingShipments;
use Kabooodle\Services\Shippr\ParcelUnits;
use Kabooodle\Services\Shippr\WeightUnits;
use Kabooodle\Services\Shippr\ParcelObject;
use Kabooodle\Http\Controllers\Web\Controller;
use Illuminate\Validation\ValidationException;
use Kabooodle\Bus\Commands\Shipping\GetShippingRatesCommand;
use Kabooodle\Services\Shippr\Exceptions\InvalidAddressException;

/**
 * Class ShippingOrderController
 * @package Kabooodle\Http\Controllers\Web\Shipping
 */
class ShippingOrderController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $shipments = user()->shippingTransactions;

        return $this->view('shipping.index')->with(compact('shipments'));
    }

    /**
     * @param Request $request
     * @param null      $claimUUID
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request, $claimUUID = null)
    {
        $claims = user()->claimsAsSellerNoShipping();

        return $this->view('shipping.order.create')->with(compact('claims'));
    }

    /**
     * @param Request $request
     * @param null    $claimUUID
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, $claimUUID = null)
    {
        try {
            $this->validate($request, $this->rules() + $this->parcelRules(), $this->errorMessages());

            $sender = Binput::get('from');
            $recipient = Binput::get('to');

            $command = new GetShippingRatesCommand(
                user(),
                array_filter(Binput::get('claim_id')),
                MailingAddress::fromArray($recipient),
                MailingAddress::fromArray($sender),
                new ParcelObject(Binput::get('parcel'))
            );
            $response = $this->dispatchNow($command);

            return redirect()->route('shipping.show', [$response->uuid]);
        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return $this->redirect()->back()->withErrors($e->validator->getMessageBag())->withInput(Binput::all());
        } catch (InvalidAddressException $e) {
                Messages::error('Invalid address: '. $e->getDescription());

                return redirect()->back()->withInput(Binput::all());
        }  catch (NoRatesFoundForParcelException $e) {
            Messages::error('No pricing is available based on the parcel data (size/weight).');

            return redirect()->back()->withInput(Binput::all());
        }
        catch (Shippo_Error $e) {
            // TODO: Gracefully handle this kind of exception.  It's really a developer-eyes-only exception.

            Messages::error('Invalid parcel details, '. $e->getMessage());

            return redirect()->back()->withInput(Binput::all());
        }
    }

    /**
     * @param Request $request
     * @param         $shipmentUUID
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, $shipmentUUID)
    {
        $shipment = ShippingShipments::where('uuid', $shipmentUUID)->first();
        $rates = $shipment['rates_list'];

        return $this->view('shipping.order.rates')->with(compact('rates', 'shipment'));
    }

    /**
     * @return array
     */
    private function rules()
    {
        return [
            'claim_id' => 'required',

            'parcel.length' => 'numeric',
            'parcel.width' => 'numeric',
            'parcel.height' => 'numeric',
            'parcel.weight' => 'required|numeric|digits_between:0,999999',
            'parcel.weight_uom' => 'required|in:'. implode(',', WeightUnits::getUnits()),
            'parcel.shipment_date' => 'date',

            'to.name' => 'required',
            'to.street1' => 'required',
            'to.city' => 'required',
            'to.state' => 'required',
            'to.zip' => 'required',
            'to.email' => 'required',

            'from.name' => 'required',
            'from.street1' => 'required',
            'from.city' => 'required',
            'from.state' => 'required',
            'from.zip' => 'required',
            'from.email' => 'required'
        ];
    }

    /**
     * @return array
     */
    private function parcelRules()
    {
        return [
            'parcel.length' => 'required_if:parcel.id,self|numeric|digits:1,999999',
            'parcel.height' => 'required_if:parcel.id,self|numeric|digits:1,999999',
            'parcel.width' => 'required_if:parcel.id,self|numeric|digits:1,999999',
            'parcel.distance_unit' => 'required_if:parcel.id,self|in:'.implode(',', ParcelUnits::getUnits())
        ];
    }

    /**
     * @return array
     */
    private function errorMessages()
    {
        return [
            'to.name.required' => 'Recipient name is required',
            'to.email.required' => 'Recipient email address is required',
            'parcel.weight.required' => 'Parcel weight is required',
            'parcel.length.required_if' => 'Parcel length is required',
            'parcel.height.required_if' => 'Parcel height is required',
            'parcel.width.required_if' => 'Parcel width is required',
            'parcel.dimensions_uom.required_if' => 'Parcel units of measurement are required',
        ];
    }
}