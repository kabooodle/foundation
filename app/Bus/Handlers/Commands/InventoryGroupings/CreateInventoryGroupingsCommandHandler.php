<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\InventoryGroupings;

use DB;
use Kabooodle\Bus\Commands\InventoryGroupings\CreateInventoryGroupingsCommand;
use Kabooodle\Bus\Events\InventoryGroupings\InventoryGroupingWasCreatedEvent;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Foundation\Exceptions\ForbiddenModelAccessException;
use Kabooodle\Models\Files;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\InventoryGrouping;

/**
 * Class CreateInventoryGroupingsCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\InventoryGroupings
 */
class CreateInventoryGroupingsCommandHandler
{
    /**
     * @param CreateInventoryGroupingsCommand $command
     *
     * @return mixed
     *
     * @throws ForbiddenModelAccessException
     * @throws RequestedQuantityCannotBeSatisfiedException
     */
    public function handle(CreateInventoryGroupingsCommand $command)
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
            if (!$item->canSatisfyRequestedQuantityOf($command->getInitialQty())) {
                throw new RequestedQuantityCannotBeSatisfiedException('Quantity of the outfit exceeds available quantity of one or more inventory items.');
            }
        }

        return DB::transaction(function () use ($command, $inventoryIds) {
            $grouping = InventoryGrouping::factory([
                'user_id' => $command->getUser()->id,
                'name' => $command->getName(),
                'description' => $command->getDescription(),
                'locked' => $command->isLocked(),
                'price_usd' => $command->getPrice(),
                'initial_qty' => $command->getInitialQty(),
                'auto_add' => $command->isAutoAdd(),
                'max_quantity' => $command->isMaxQuantity(),
            ]);

            $grouping->inventoryItems()->sync($inventoryIds);

            $imageData = $command->getImage();

            $file = Files::create([
                'location' => array_get($imageData, 'location'),
                'key' => array_get($imageData, 'key'),
                'bucket_name' => array_get($imageData, 'bucket'),
                'fileable_type' => get_class($grouping),
                'fileable_id' => $grouping->id
            ]);

            $grouping->cover_photo_file_id = $file->id;

            if ($command->getCategories()) {
                $grouping->tag($command->getCategories());
            }

            $grouping->save();

            event(new InventoryGroupingWasCreatedEvent($grouping));

            return $grouping;
        });
    }
}
