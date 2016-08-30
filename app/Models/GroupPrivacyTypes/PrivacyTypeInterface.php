<?php

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