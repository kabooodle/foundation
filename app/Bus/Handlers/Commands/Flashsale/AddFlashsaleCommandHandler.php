<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use DB;
use Kabooodle\Models\User;
use Kabooodle\Models\FlashSales;
use Kabooodle\Bus\Commands\Flashsale\AddFlashsaleCommand;
use Kabooodle\Bus\Events\Flashsale\FlashsaleWasCreatedEvent;

/**
 * Class AddFlashsaleCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Flashsale
 */
class AddFlashsaleCommandHandler extends AbstractFlashsaleCommandHandler
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
                $file = $this->buildCoverPhotoFromAWSData($image, $flashsale);
                $flashsale->coverimage()->save($file);
            }

            // Add seller groups
            if ($command->getSellerGroups()) {
                $groups = $this->normalizeSellerGroups($command->getSellerGroups());
                $flashsale->sellerGroups()->sync($groups, true);
            }

            event(new FlashsaleWasCreatedEvent($flashsale->id));

            return $flashsale;
        });
    }
}
