<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Listings;

/**
 * Class ListingsPresenterTrait
 */
trait ListingsPresenterTrait
{
    /**
     * @return string
     */
    public function getStatus()
    {
        return listingStatusHtml($this->entity->status);
    }
}
