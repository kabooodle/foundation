<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Shippr;

/**
 * Class WeightUnits
 * @package Kabooodle\Services\Shippr
 */
final class WeightUnits
{
    /**
     * @return array
     */
    public static function getUnits()
    {
        $units = [
            'lb',
            'oz',
            'g',
            'kg'
        ];

        return array_combine($units, $units);
    }
}
