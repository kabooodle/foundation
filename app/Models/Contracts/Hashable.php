<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

/**
 * Interface Hashable
 * @package Kabooodle\Models\Contracts
 */
interface Hashable
{
    /**
     * @return string
     */
    public function makeHashedResourceString(): string;
}
