<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\InventoryGroupings;

use DB;
use Kabooodle\Bus\Commands\InventoryGroupings\UpdateInventoryGroupingCommand;
use Kabooodle\Bus\Events\InventoryGroupings\InventoryGroupingWasUpdatedEvent;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Foundation\Exceptions\ForbiddenModelAccessException;
use Kabooodle\Models\Files;
use Kabooodle\Models\Inventory;

/**
 * Class UpdateInventoryGroupingCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\InventoryGroupings
 */
class UpdateInventoryGroupingCommandHandler
{
    /**
     * @param UpdateInventoryGroupingCommand $command
     *
     * @return mixed
     *
     * @throws ForbiddenModelAccessException
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(UpdateInventoryGroupingCommand $command)
    {
        $user = $command->getUser();
        $inventoryIds = [];
        $inventoryData = $command->getInventory();

        foreach($inventoryData as $data) {
            $inventoryIds[] = $data['id'];
        }

        $inventoryItems = Inventory::whereIn('id', $inventoryIds)->get()->filter(function ($item) use ($user) {
            return $item->user_id == $user->id;
        });

        if (count($inventoryIds) != $inventoryItems->count()) {
            throw new ForbiddenModelAccessException('Not all inventory items belong to the current user.');
        }

        foreach ($inventoryItems as $item) {
            if (!$item->canSatisfyRequestedQuantityOf($command->getInitialQty() - $command->getGrouping()->initial_qty)) {
                throw new RequestedQuantityCannotBeSatisfiedException('Quantity of the outfit exceeds available quantity of one or more inventory items.');
            }
        }

        return DB::transaction(function () use ($command, $inventoryIds) {
            $grouping = $command->getGrouping();
            $grouping->name = $command->getName();
            $grouping->description = $command->getDescription();
            $grouping->locked = $command->isLocked();
            $grouping->price_usd = $command->getPrice();
            $grouping->initial_qty = $command->getInitialQty();
            $grouping->auto_add = $command->isAutoAdd();
            $grouping->max_quantity = $command->isMaxQuantity();

            $imageData = $command->getImage();

            if (is_numeric($imageData['id'])) {
                $grouping->cover_photo_file_id = $imageData['id'];
            } else {
                $grouping->files()->delete();

                $newFile = Files::create([
                    'location' => $imageData['location'],
                    'key' => $imageData['key'],
                    'bucket_name' => $imageData['bucket'],
                    'fileable_type' => get_class($grouping),
                    'fileable_id' => $grouping->id
                ]);

                $grouping->cover_photo_file_id = $newFile->id;
            }

            if ($command->getCategories()) {
                $grouping->tag($command->getCategories());
            }

            $grouping->save();

            $grouping->inventoryItems()->sync($inventoryIds);

            event(new InventoryGroupingWasUpdatedEvent($grouping));

            return $grouping;
        });
    }
}
