<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use DB;
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
        return DB::transaction(function() use ($command) {
            $flashsale = FlashSales::factory([
                'user_id' => $command->getUser()->id,
                'host_id' => $command->getUser()->id,
                'name' => $command->getName(),
                'description' => $command->getDescription(),
                'starts_at' => $command->getStartsAndEndsAt()->getStartsAt(),
                'ends_at' => $command->getStartsAndEndsAt()->getEndsAt(),
                'privacy' => $command->getPrivacy(),
            ]);

            if ($command->getSellerGroups()) {
                $groups = [];
                foreach ($command->getSellerGroups() as $group) {
                    $groupId = isset($group['id']) ? $group['id'] : false;
                    $slot = isset($group['time_slot']) ? $group['time_slot'] : null;
                    if ($groupId) {
                        $groups[$group['id']]['time_slot'] = $slot;
                    }
                }
                $flashsale->sellerGroups()->sync($groups, true);
            }

//        event(new FlashsaleWasCreatedEvent($flashsale));

            return $flashsale;
        });
    }
}
