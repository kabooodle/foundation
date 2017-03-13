<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Shippr;

/**
 * Class ParcelUnits
 * @package Kabooodle\Services\Shippr
 */
final class ParcelUnits
{
    /**
     * @return array
     */
    public static function getUnits()
    {
        $units = [
            'in',
            'ft',
            'mm',
            'm',
            'yd'
        ];

        return array_combine($units, $units);
    }
}
