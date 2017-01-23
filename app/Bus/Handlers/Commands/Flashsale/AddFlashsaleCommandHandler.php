<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Files;
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

            // Add admins
            if ($adminIds = $command->getAdminIds()) {
                $users = User::whereIn('id', $adminIds)->get();
                $flashsale->admins()->sync($users->pluck('id')->toArray(), true);
            }

            // Add cover photo
            if ($image = $command->getCoverPhoto()) {
                $image = json_decode($image['json'], true);
                $file = Files::create([
                    'location' => $image['location'],
                    'key' => $image['key'],
                    'bucket_name' => $image['bucket'],
                    'fileable_type' => get_class($flashsale),
                    'fileable_id' => $flashsale->id
                ]);

                // Associate files(images) to the item.
                $flashsale->coverimage()->save($file);
            }

            // Add seller groups
            if ($command->getSellerGroups()) {
                $groups = [];
                foreach ($command->getSellerGroups() as $group) {
                    $groupId = isset($group['id']) ? $group['id'] : false;
                    $slot = (isset($group['time_slot']) && $group['time_slot'] <> '') ?  Carbon::parse($group['time_slot']) : null;
                    if ($groupId) {
                        $groups[$group['id']]['time_slot'] = $slot;
                    }
                }

                $flashsale->sellerGroups()->sync($groups, true);
            }

            event(new FlashsaleWasCreatedEvent($flashsale->id));

            return $flashsale;
        });
    }
}
