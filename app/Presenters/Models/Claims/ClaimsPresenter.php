<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Claims;

use Kabooodle\Models\Claims;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class ClaimsPresenter
 * @package Kabooodle\Presenters\Models\Claims
 */
class ClaimsPresenter extends PresenterAbstract
{
    /**
     * @param bool $statusAsBuyerPov
     * @return string
     */
    public function getShippingStatus($statusAsBuyerPov = false)
    {
        /** @var Claims $claim */
        $claim = $this->entity;

        // If we already have a shipping transaction, return the status.
        if ($claim->shipmentTransaction()) {
            return $claim->shipmentTransaction()->present()->getStatus();
        }

        // If this claim is queued to ship
        if ($claim->queuedToShip()) {
            if ($statusAsBuyerPov) {
                return 'Pending Action';
            }
            return '<a class="btn white btn-xs" href="'.route('shipping.create').'?c='.$claim->id.'">Create Label</a>';
        }

        return '<span class="">Externally Shipped</span>';
    }
}
