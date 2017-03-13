<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Shippr;

use Shippo;
use Shippo_Util;
use Shippo_Parcel;
use Shippo_Object;
use Shippo_Address;
use Shippo_Shipment;
use Shippo_Transaction;
use Shippo_ApiRequestor;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Models\ShippingPurpose;
use Kabooodle\Services\Shippr\Exceptions\InvalidAddressException;

/**
 * Class ShipprService
 * @package Kabooodle\Services\Shippr
 */
class ShipprService
{
    /**
     * ShipprService constructor.
     */
    public function __construct()
    {
        Shippo::setApiKey(env('SHIPPO_PRIVATE'));
    }

    /**
     * TODO: This only validates US addresses.  Modify to allow international.
     *
     * @param MailingAddress $address
     * @param string         $purpose
     *
     * @return Shippo_Object
     *
     * @throws InvalidAddressException
     */
    public function createAndValidateAddress(MailingAddress $address, $purpose = ShippingPurpose::PURPOSE_PURCHASE)
    {
        $toAddress = $address->toArray();
        $toAddress['validate'] = true;
        $toAddress['object_purpose'] = (string) $purpose;
        $toAddress['country'] = 'US';

        $response = Shippo_Address::create($toAddress);

        if ($response['object_state'] <> 'VALID') {
            $firstMessage = $response['messages'][0];
            throw new InvalidAddressException($firstMessage['code'], $firstMessage['text']);
        }

        return $response;
    }

    /**
     * @param $length
     * @param $height
     * @param $width
     * @param string $distanceUom
     * @param $weight
     * @param string $weightUom
     *
     * @return Shippo_Object
     *
     * @throws InvalidAddressException
     */
    public function createAndValidateParcel($length, $width, $height, $distanceUom, $weight, $weightUom)
    {
        $response = Shippo_Parcel::create([
            'length' => $length,
            'height' => $height,
            'width' => $width,
            'distance_unit' => $distanceUom,
            'weight' => $weight,
            'mass_unit' => $weightUom
        ]);

        if ($response['object_state'] <> 'VALID') {
            $firstMessage = $response['messages'][0];
            throw new InvalidAddressException($firstMessage['code'], $firstMessage['text']);
        }

        return $response;
    }

    /**
     * This determines available shipping rates based on the information given.
     *
     * TODO: This has static insurance information, perhaps allow the end user to set these params.
     *
     * @param Shippo_Object $to
     * @param Shippo_Object $from
     * @param Shippo_Object $parcel
     *
     * @return Shippo_Shipment|Shippo_Object
     */
    public function createShipment(Shippo_Object $to,  Shippo_Object $from, Shippo_Object $parcel)
    {
        return Shippo_Shipment::create([
            'object_purpose' => ShippingPurpose::PURPOSE_PURCHASE,
            'address_from' => $from['object_id'],
            'address_to' => $to['object_id'],
            'parcel' => $parcel['object_id'],
            'insurance_amount' => 0,
            'insurance_currency' => 'USD',
            'async' => false
        ]);
    }

    /**
     * @param string        $rateId
     * @param string        $type
     *
     * @return Shippo_Transaction
     */
    public function createLabel($rateId, $type = 'PDF_4X6')
    {
        return Shippo_Transaction::create([
            'rate' => $rateId,
            'label_file_type' => 'PDF_4X6',
            'async' => false
        ]);
    }

    /**
     * @param string $carrier
     * @param string $trackingNumber
     *
     * @return array|Shippo_Object
     */
    public function registerTrackingWebhook(string $carrier = 'usps', string $trackingNumber)
    {
        $requestor = new Shippo_ApiRequestor;
        list($response, $apiKey) = $requestor->request('post', '/tracks/', [
            'carrier' => $carrier,
            'tracking_number' => $trackingNumber
        ]);

        return Shippo_Util::convertToShippoObject($response, $apiKey);
    }
}