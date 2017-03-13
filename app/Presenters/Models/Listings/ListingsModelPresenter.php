<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Listings;

use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class ListingsModelPresenter
 */
class ListingsModelPresenter extends PresenterAbstract
{
    use ListingsPresenterTrait;

    public function listingParentName()
    {
        return $this->entity->sale_name;
    }
}
