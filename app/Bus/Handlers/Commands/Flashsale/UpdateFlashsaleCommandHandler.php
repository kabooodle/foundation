<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use DB;
use Kabooodle\Models\FlashSales;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Flashsale\FlashsaleWasUpdatedEvent;
use Kabooodle\Bus\Commands\Flashsale\UpdateFlashsaleCommand;

/**
 * Class UpdateFlashsaleCommandHandler
 * @package Kabooodle\Bus\Commands\Flashsale
 */
class UpdateFlashsaleCommandHandler extends AbstractFlashsaleCommandHandler
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
                $file = $this->buildCoverPhotoFromAWSData($image, $flashsale);
                $existingCoverPhoto->delete();
                $flashsale->coverimage()->save($file);
            }

            // Add seller groups
            if ($command->getSellerGroups()) {
                $groups = $this->normalizeSellerGroups($command->getSellerGroups());
                $flashsale->sellerGroups()->sync($groups, true);
            }

            $flashsale->save();

            event(new FlashsaleWasUpdatedEvent($flashsale->id));

            return $flashsale;
        });
    }
}
