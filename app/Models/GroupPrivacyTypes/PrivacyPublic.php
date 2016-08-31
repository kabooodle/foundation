<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\GroupPrivacyTypes;

/**
 * Class PrivacyPublic
 * @package Kabooodle\Models\GroupPrivacyTypes
 */
class PrivacyPublic implements PrivacyTypeInterface
{
    /**
     * @return string
     */
    public function name()
    {
        return 'public';
    }

    /**
     * @return string
     */
    public function description()
    {
        return '';
    }
}