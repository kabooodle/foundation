<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
    public function getNameOfShoppable(): string;

    /**
     * @return BelongsTo
     */
    public function listedItem(): BelongsTo;
}
