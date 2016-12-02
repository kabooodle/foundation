<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Interface ShoppableInterface
 * @package Kabooodle\Models\Contracts
 */
interface ShoppableInterface
{
    /**
     * @return string
     */
    public function getNameOfResource(): string;

    /**
     * @return BelongsTo
     */
    public function inventoryItem(): BelongsTo;
}
