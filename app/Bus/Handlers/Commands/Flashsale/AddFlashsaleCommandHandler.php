<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use Kabooodle\Models\FlashSales;
use Kabooodle\Bus\Commands\Flashsale\AddFlashsaleCommand;
use Kabooodle\Bus\Events\Flashsale\FlashsaleWasCreatedEvent;

/**
 * Class AddFlashsaleCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Flashsale
 */
class AddFlashsaleCommandHandler
{
    /**
     * @param AddFlashsaleCommand $command
     *
     * @return FlashSales
     */
    public function handle(AddFlashsaleCommand $command)
    {
        if ($command->getType() == FlashSales::TYPE_GROUP) {
            $hostId = $command->getUser()->allMyGroups()->find($command->getHostId());
        } else {
            $hostId = $command->getUser()->id;
        }

        $flashsale = FlashSales::factory([
            'user_id' => $command->getUser()->id,
            'name' => $command->getName(),
            'description' => $command->getDescription(),
            'starts_at' => $command->getStartTime(),
            'ends_at' => $command->getEndTime(),
            'type' => $command->getType(),
            'seller_rules' => $command->getSellerRules(),
            'host_id' => $hostId
        ]);

        event(new FlashsaleWasCreatedEvent($flashsale));

        return $flashsale;
    }
}