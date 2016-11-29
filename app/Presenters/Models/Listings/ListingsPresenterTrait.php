<?php

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