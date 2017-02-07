<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
        $inventoryIds = $command->getInventoryIds();

        $inventoryItems = Inventory::whereIn('id', $inventoryIds)->get()->filter(function ($item) use ($user) {
            return $item->user_id == $user->id;
        });

        if (count($inventoryIds) != $inventoryItems->count()) {
            throw new ForbiddenModelAccessException('Not all grouping items belong to the current user.');
        }

        foreach ($inventoryItems as $item) {
            if (!$item->canSatisfyRequestedQuantityOf($command->getInitialQty())) {
                throw new RequestedQuantityCannotBeSatisfiedException('Initial quantity of grouping exceeds satisfiable quantity of one or more grouping items.');
            }
        }

        return DB::transaction(function () use ($command) {
            $grouping = $command->getGrouping();
            $grouping->name = $command->getName();
            $grouping->description = $command->getDescription();
            $grouping->locked = $command->isLocked();
            $grouping->price_usd = $command->getPrice();
            $grouping->initial_qty = $command->getInitialQty();

            $currentFile = Files::whereKey($command->getImages()['key'])->first();

            if ($currentFile) {
                $grouping->cover_photo_file_id = $currentFile->id;
            } else {
                $newFile = Files::create([
                    'location' => $command->getImages()['location'],
                    'key' => $command->getImages()['key'],
                    'bucket_name' => $command->getImages()['bucket'],
                    'fileable_type' => get_class($grouping),
                    'fileable_id' => $grouping->id
                ]);

                $grouping->files()->save($newFile);

                $grouping->cover_photo_file_id = $newFile->id;
            }

            if ($command->getCategories()) {
                $grouping->tag($command->getCategories());
            }

            $grouping->save();

            event(new InventoryGroupingWasUpdatedEvent($grouping));

            return $grouping;
        });
    }
}
