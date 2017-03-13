<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Shipping;

use DB;
use Kabooodle\Foundation\Exceptions\Shippo\NoRatesFoundForParcelException;
use Shippo_Object;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Models\ShippingPurpose;
use Kabooodle\Models\ShippingShipments;
use Kabooodle\Services\Shippr\ShipprService;
use Kabooodle\Bus\Commands\Shipping\GetShippingRatesCommand;

/**
 * Class GetShippingRatesCommand
 * @package Kabooodle\Bus\Handlers\Commands\Shipping
 */
class GetShippingRatesCommandHandler
{
    /**
     * GetShippingRatesCommandHandler constructor.
     *
     * @param ShipprService $shippr
     */
    public function __construct(ShipprService $shippr)
    {
        $this->shippr = $shippr;
    }

    /**
     * @param GetShippingRatesCommand $command
     *
     * @return ShippingShipments
     */
    public function handle(GetShippingRatesCommand $command)
    {
        $recipient = $command->getRecipient();
        $sender = $command->getSender();
        $parcel = $command->getParcel();

        $weight = $parcel['weight'];
        $weightUOM = $parcel['weight_uom'];
        if ($parcel['template']) {
            $template =  $parcel['template'];
        }
        $length = $parcel['length'];
        $width = $parcel['width'];
        $height = $parcel['height'];
        $dimensionsUom = $parcel['distance_unit'];

        $parcel = $this->validateParcel($length, $width, $height, $dimensionsUom, $weight, $weightUOM);
        if (isset($template)) {
            $parcel['template'] = $template;
        }

        $recipient = $this->validateAddress($recipient, ShippingPurpose::PURPOSE_PURCHASE);
        $sender = $this->validateAddress($sender, ShippingPurpose::PURPOSE_PURCHASE);

        $shipment = $this->shippr->createShipment($recipient, $sender, $parcel);

        if ($shipment['rates_list'] == [] || count($shipment['rates_list']) ==0) {
            throw new NoRatesFoundForParcelException;
        }

        return $this->createShipmentsEntity($shipment, $parcel, $command);
    }

    /**
     * @param $attributes
     * @param $parcel
     * @param GetShippingRatesCommand $command
     * @return mixed
     */
    public function createShipmentsEntity($attributes, $parcel, GetShippingRatesCommand $command)
    {
        return DB::transaction(function () use ($attributes, $command, $parcel) {
            $shipmentDB = new ShippingShipments;
            $shipmentDB->user_id = $command->getActor()->id;
            $shipmentDB->shipping_parcel_template_id = isset($parcel['template']) ? $parcel['template']['id'] : null;
            $shipmentDB->shipment_id = $attributes['object_id'];
            $shipmentDB->recipient_id = $attributes['address_to'];
            $shipmentDB->recipientData = $command->getRecipient()->toArray();
            $shipmentDB->sender_id = $attributes['address_from'];
            $shipmentDB->senderData = $command->getSender()->toArray();
            $shipmentDB->parcel_id = $attributes['parcel'];
            $shipmentDB->parcel_data = $parcel->__toArray();
            $shipmentDB->status = $attributes['object_status'];
            $shipmentDB->shipment_state = $attributes['object_state'];
            $shipmentDB->rates_url = $attributes['rates_url'];
            $shipmentDB->rates_list = $this->convertRatesToArray($attributes['rates_list']);
            $shipmentDB->messages = $attributes['messages'];
            $shipmentDB->save();

            $shipmentDB->claims()->attach($command->getClaimIds());

            return $shipmentDB;
        });
    }

    /**
     * @param $rates
     *
     * @return array
     */
    protected function convertRatesToArray($rates)
    {
        $x = [];
        /** @var Shippo_Object $y */
        foreach ($rates as $y) {
            $x[] = $y->__toArray();
        }

        return $x;
    }

    /**
     * @param $length
     * @param $width
     * @param $height
     * @param $dimensionsUom
     * @param $weight
     * @param $weightUOM
     *
     * @return Shippo_Object
     */
    public function validateParcel($length, $width, $height, $dimensionsUom, $weight, $weightUOM)
    {
        return $this->shippr->createAndValidateParcel($length, $width, $height, $dimensionsUom, $weight, $weightUOM);
    }

    /**
     * @param MailingAddress $recipient
     * @param string         $purpose
     *
     * @return Shippo_Object
     */
    public function validateAddress(MailingAddress $recipient, $purpose = ShippingPurpose::PURPOSE_PURCHASE)
    {
        return $this->shippr->createAndValidateAddress($recipient, $purpose);
    }
}
