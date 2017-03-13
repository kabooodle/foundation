<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class ShippingPurpose
 * @package Kabooodle\Models
 */
final class ShippingPurpose
{
    const PURPOSE_QUOTE = 'QUOTE';
    const PURPOSE_PURCHASE = 'PURCHASE';

    /**
     * @var array
     */
    protected $purposes = [
        self::PURPOSE_PURCHASE,
        self::PURPOSE_QUOTE
    ];

    /**
     * @var
     */
    protected $purpose;

    /**
     * ShippingPurpose constructor.
     *
     * @param $purposeName
     */
    public function __construct($purposeName)
    {
        $this->validatePurpose($purposeName);
        $this->mapToPurpose($purposeName);
    }

    // immutable properties bitch
    public function __set($k, $v)
    {
    }

    /**
     * @return mixed
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @return array
     */
    protected function getAvailablePurposes()
    {
        return $this->purposes;
    }

    /**
     * @param $purposeName
     */
    private function mapToPurpose($purposeName)
    {
        $this->purpose = array_filter($this->getAvailablePurposes(), function ($v) use ($purposeName) {
            return $v === strtoupper($purposeName);
        });
    }

    /**
     * @param $purposeName
     *
     * @return bool
     */
    private function validatePurpose($purposeName)
    {
        if (! in_array($purposeName, $this->getAvailablePurposes())) {
            throw new \InvalidArgumentException("Shipping Purpose {$purposeName} is invalid");
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->getPurpose();
    }
}
