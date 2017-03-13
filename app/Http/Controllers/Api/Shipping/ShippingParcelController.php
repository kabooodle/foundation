<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Shipping;

use Binput;
use Shippo_Error;
use Illuminate\Http\Request;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Models\ShippingShipments;
use Kabooodle\Services\Shippr\WeightUnits;
use Kabooodle\Services\Shippr\ParcelUnits;
use Kabooodle\Services\Shippr\ParcelObject;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Shipping\GetShippingRatesCommand;
use Kabooodle\Services\Shippr\Exceptions\InvalidAddressException;
use Kabooodle\Foundation\Exceptions\Shippo\NoRatesFoundForParcelException;

/**
 * Class ShippingParcelController
 */
class ShippingParcelController extends AbstractApiController
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
            $this->validate($request, $this->rules() + $this->parcelRules(), $this->errorMessages());

            $sender = Binput::get('from');
            $recipient = Binput::get('to');

            $command = new GetShippingRatesCommand(
                $this->getUser(),
                array_filter(Binput::get('claim_id')),
                MailingAddress::fromArray($recipient),
                MailingAddress::fromArray($sender),
                new ParcelObject(Binput::get('parcel'))
            );

            /** @var ShippingShipments $shipment */
            $shipment = $this->dispatchNow($command);
            $rates = $shipment['rates_list'];

            return $this->setData([
                'msg' => '',
                'rates' => $rates,
                'shipment' => $shipment
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setData([
                'msg' => 'Some fields require input!',
                'errors' => $e->validator->getMessageBag()
            ])->setStatusCode(400)->respond();
        } catch (InvalidAddressException $e) {
            return $this->setData([
                'msg' => 'Invalid address: '. $e->getDescription(),
            ])->setStatusCode(400)->respond();
        } catch (NoRatesFoundForParcelException $e) {
            return $this->setData([
                'msg' => 'No pricing is available based on the parcel data (size/weight).',
            ])->setStatusCode(400)->respond();
        } catch (Shippo_Error $e) {
            $msg = 'An error occurred, please try again';
            $body = $e->getJsonBody();

            // So we need to handle specific error messages in a certain way.
            // Only share certain ones with end user, else, log the error and return
            // generic error.
            if ($body && is_array($body) && array_key_exists('__all__', $body)) {
                $msg = $body['__all__'][0];
                if ($msg == 'Invalid parcel details, From and To Addresses are identical. Addresses must be different to create a valid shipment.') {
                    $msg = 'From and To Addresses are identical';
                }
            }

            return $this->setData([
                'msg' => 'Invalid parcel details, '. $msg,
            ])->setStatusCode(400)->respond();
        }
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
            'to.phone' => 'required',

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
            'claim_id.required' => 'An approved claim must be selected',
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
