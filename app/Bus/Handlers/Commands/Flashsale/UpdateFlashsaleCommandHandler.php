<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use Kabooodle\Models\FlashSales;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Flashsale\UpdateFlashsaleCommand;
use Kabooodle\Bus\Commands\Flashsale\InviteSellerToFlashSaleCommand;

/**
 * Class UpdateFlashsaleCommandHandler
 * @package Kabooodle\Bus\Commands\Flashsale
 */
class UpdateFlashsaleCommandHandler
{
    use DispatchesJobs;

    /**
     * @param UpdateFlashsaleCommand $command
     *
     * @return FlashSales
     */
    public function handle(UpdateFlashsaleCommand $command)
    {
        $flashsale = $command->getFlashSale();
        $flashsale->name = $command->getName();
        $flashsale->description = $command->getDescription();
        $flashsale->starts_at = $command->getStartTime();
        $flashsale->ends_at = $command->getEndTime();
        $flashsale->privacy = $command->getPrivacy();
        $flashsale->seller_rules = $command->getSellerRules();
        $flashsale->admins()->sync($command->getAdminIds());

        $flashsale->save();

        if ($command->getInvitedSellerEmails()) {
            foreach ($command->getInvitedSellerEmails() as $email) {
                $this->dispatchNow(new InviteSellerToFlashSaleCommand($flashsale, $command->getUser(), $email));
            }
        }

        return $flashsale;
    }
}
