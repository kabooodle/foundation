<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

use \Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface WatchableInterface
 */
interface WatchableInterface
{
    /**
     * @return MorphMany
     */
    public function watchers(): MorphMany;
}
