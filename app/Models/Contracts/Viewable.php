<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;
use Kabooodle\Models\BaseEloquentModel;

/**
 * Interface Viewable
 * @package Kabooodle\Models\Contracts
 */
interface Viewable
{
    /**
     * @return bool
     */
    public function hasViewableChild(): bool;

    /**
     * @return BaseEloquentModel|null
     */
    public function getViewableChild();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function views();
}
