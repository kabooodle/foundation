<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\GroupPrivacyTypes;

/**
 * Interface PrivacyTypeInterface
 * @package Kabooodle\Models\GroupPrivacyTypes
 */
interface PrivacyTypeInterface
{
    /**
     * @return string
     */
    public function name();

    /**
     * @return string
     */
    public function description();
}
