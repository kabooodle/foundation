<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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

    public function getClaimStatus()
    {
        /** @var Claims $claim */
        $claim = $this->entity;

        if ($claim->wasRejected()) {
            return '<span class="text-pink-500">'.$claim->claim_status.'</span>';
        }

        if ($claim->wasAccepted()) {
            return '<span class="">'.$claim->claim_status.'</span>';
        }

        return '<span class="text-warn">'.$claim->claim_status.'</span>';
    }

    /**
     * @param bool $statusAsBuyerPov
     * @return string
     */
    public function getShippingStatus($statusAsBuyerPov = false)
    {
        /** @var Claims $claim */
        $claim = $this->entity;

        // Rejected and Buyer POV?
//        if ($statusAsBuyerPov && ($claim->wasRejected() || $claim->isPending())) {
//            return '<span class=""></span>';
//        }

        // If we already have a shipping transaction, return the status.
        if ($claim->shipmentTransaction()) {
            return $claim->shipmentTransaction()->present()->getStatus();
        }

        // If this claim is queued to ship
        if (!$statusAsBuyerPov && $claim->queuedToShip()) {
            return '<a class="btn white btn-xs" href="'.route('merchant.shipping.create').'?c='.$claim->id.'">Create Label</a>';
        }

        if ($claim->shippedManually()) {
            return '<span class="">Externally Shipped</span>';
        }

        return '<span class="">Pending Seller Action</span>';
    }
}
