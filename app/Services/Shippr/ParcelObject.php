<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Shippr;

use ArrayAccess;
use Kabooodle\Models\ShippingParcelTemplates;

/**
 * Class ParcelObject
 * @package Kabooodle\Services\Shippr
 */
class ParcelObject implements ArrayAccess
{
    protected $id;
    protected $parcelId;
    protected $parcelTemplate = false;
    protected $template;
    protected $length;
    protected $width;
    protected $height;
    protected $distance_unit;
    protected $weight;
    protected $massUnit;
    protected $weight_uom;

    /**
     * ParcelObject constructor.
     *
     * @param array $shippoObject
     */
    public function __construct(array $shippoObject)
    {
        $this->shippoParcelObject = $shippoObject;
        $this->mapValuesToProperties($shippoObject);
    }

    /**
     * @param array $shippo
     */
    protected function mapValuesToProperties(array $shippo)
    {
        // If the incoming parcel is a parcel, get data from there.
        if ($shippo['id'] <> 'self') {
            $template = ShippingParcelTemplates::where('parcel_id', $shippo['id'])->firstOrFail();
            $shippo = $template->toArray() + $shippo;
            $this->parcelTemplate = $this->template = $template->toArray();
        }

        $this->parcelId = $this->id = array_get($shippo, 'object_id', $this->parcelTemplate['id']);
        $this->length = $shippo['length'];
        $this->width = $shippo['width'];
        $this->height = $shippo['height'];
        $this->distance_unit = $shippo['distance_unit'];
        $this->weight = $shippo['weight'];
        $this->massUnit = $this->weight_uom = $shippo['weight_uom'];
    }

    /**
     * @return array
     */
    public function getShippoParcelObject()
    {
        return $this->shippoParcelObject;
    }

    /**
     * @return mixed
     */
    public function getParcelId()
    {
        return $this->parcelId;
    }

    /**
     * @return mixed
     */
    public function getParcelTemplate()
    {
        return $this->parcelTemplate;
    }

    /**
     * @return mixed
     */
    public function getLenght()
    {
        return $this->length;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return mixed
     */
    public function getDistanceUnit()
    {
        return $this->distance_unit;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return mixed
     */
    public function getMassUnit()
    {
        return $this->massUnit;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return property_exists($offset, $this);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }
}
