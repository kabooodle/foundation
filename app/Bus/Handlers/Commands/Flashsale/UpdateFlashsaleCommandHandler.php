<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use DB;
use Carbon\Carbon;
use Kabooodle\Bus\Events\Flashsale\FlashsaleWasUpdatedEvent;
use Kabooodle\Models\FlashSales;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Flashsale\UpdateFlashsaleCommand;

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
        return DB::transaction(function() use ($command) {

            /** @var FlashSales $flashsale */
            $flashsale = $command->getFlashSale();

            $flashsale->name = $command->getName();
            $flashsale->description = $command->getDescription();
            $flashsale->privacy = $command->getPrivacy();
            $flashsale->admins()->sync($command->getAdminIds());

            // Check if we keep existing coverimage or update/replace.
            $existingCoverPhoto = $flashsale->coverimage;
            $image = $command->getCoverPhoto();
            if ($image['id'] <> $existingCoverPhoto->id) {
                $image = json_decode($image['json'], true);
                $file = Files::create([
                    'location' => $image['location'],
                    'key' => $image['key'],
                    'bucket_name' => $image['bucket'],
                    'fileable_type' => get_class($flashsale),
                    'fileable_id' => $flashsale->id
                ]);

                // Associate files(image) to the item.
                $flashsale->coverimage()->save($file);
            }

            // Add seller groups
            if ($command->getSellerGroups()) {
                $groups = [];
                foreach ($command->getSellerGroups() as $group) {
                    $groupId = isset($group['id']) ? $group['id'] : false;
                    $slot = (isset($group['time_slot']) && $group['time_slot'] <> '') ? Carbon::parse($group['time_slot']) : null;
                    if ($groupId) {
                        $groups[$group['id']]['time_slot'] = $slot;
                    }
                }

                $flashsale->sellerGroups()->sync($groups, true);
            }

            $flashsale->save();

            event(new FlashsaleWasUpdatedEvent($flashsale->id));

            return $flashsale;
        });
    }
}
