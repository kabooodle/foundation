<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\InventoryGroupings;

use DB;
use Kabooodle\Bus\Commands\InventoryGroupings\CreateInventoryGroupingCommand;
use Kabooodle\Bus\Events\InventoryGroupings\InventoryGroupingWasCreatedEvent;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Foundation\Exceptions\ForbiddenModelAccessException;
use Kabooodle\Models\Files;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\InventoryGrouping;

/**
 * Class CreateInventoryGroupingCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\InventoryGroupings
 */
class CreateInventoryGroupingCommandHandler
{
    /**
     * @param CreateInventoryGroupingCommand $command
     *
     * @return mixed
     *
     * @throws ForbiddenModelAccessException
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(CreateInventoryGroupingCommand $command)
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
            $grouping = InventoryGrouping::factory([
                'user_id' => $command->getUser()->id,
                'name' => $command->getName(),
                'description' => $command->getDescription(),
                'locked' => $command->isLocked(),
                'price_usd' => $command->getPrice(),
                'initial_qty' => $command->getInitialQty(),
            ]);

            $file = Files::create([
                'location' => $command->getImages()['location'],
                'key' => $command->getImages()['key'],
                'bucket_name' => $command->getImages()['bucket'],
                'fileable_type' => get_class($grouping),
                'fileable_id' => $grouping->id
            ]);

            $grouping->files()->save($file);

            if ($command->getCategories()) {
                $grouping->tag($command->getCategories());
            }

            $grouping->cover_photo_file_id = $file->id;

            $grouping->save();

            event(new InventoryGroupingWasCreatedEvent($grouping));

            return $grouping;
        });
    }
}
