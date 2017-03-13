<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Flashsales;

use Bugsnag;
use Illuminate\Bus\Queueable;
use Kabooodle\Models\FlashSales;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Flashsale\FlashsaleWasUpdatedEvent;

/**
 * Class HandleFlashsaleChangeOfAdminAndSellers
 */
final class HandleFlashsaleChangeOfAdminAndSellers implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param FlashsaleWasUpdatedEvent $event
     */
    public function handle(FlashsaleWasUpdatedEvent $event)
    {
        try {
            /** @var FlashSales $flashsale */
            $flashsale = FlashSales::with(['sellerGroups', 'admins'])->findOrFail($event->getFlashsaleId());
            $admins = $flashsale->admins;
            $owner = $flashsale->owner;

            // Delete listings where the seller was removed from the flashsale

            // Notify newly added sellers/admins.
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}
