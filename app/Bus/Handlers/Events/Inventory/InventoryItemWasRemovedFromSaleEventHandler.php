<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Inventory;

use Kabooodle\Models\User;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\FlashSales;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasRemovedFromSaleEvent;

/**
 * Class InventoryItemWasRemovedFromSale
 * @package Kabooodle\Bus\Events\Inventory
 */
class InventoryItemWasRemovedFromSaleEventHandler
{
    use DispatchesJobs;

    /**
     * GroupWasCreatedEventHandler constructor.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param InventoryItemWasRemovedFromSaleEvent $event
     */
    public function handle(InventoryItemWasRemovedFromSaleEvent $event)
    {
        //        $user = $event->getUser();
//        $flashsale = $event->getFlashsale();
//        $item = $event->getInventoryItem();
//        $this->mailer->queue('inventory.emails.flashsale.removed', ['flashsale' => $flashsale, 'item' => $item], function ($m) use ($user) {
//            $m->to($user->email)->subject('Item removed from flash sale on '.env('APP_NAME'));
//        });
    }
}
