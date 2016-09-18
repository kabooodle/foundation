<?php

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