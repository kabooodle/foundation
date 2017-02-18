<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
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
        return DB::transaction(function () use ($command) {
            $groupings = [];
            $user = $command->getUser();
            $groupingsData = $command->getGroupings();
            foreach ($groupingsData as $groupingData) {
                $inventoryIds = [];
                $inventoryData = array_get($groupingData, 'inventory', []);

                foreach($inventoryData as $data) {
                    $inventoryIds[] = $data['id'];
                }

                $inventoryItems = Inventory::whereIn('id', $inventoryIds)->get()->filter(function ($item) use ($user) {
                    return $item->user_id == $user->id;
                });

                if (count($inventoryIds) != $inventoryItems->count()) {
                    throw new ForbiddenModelAccessException('Not all grouping items belong to the current user.');
                }

                foreach ($inventoryItems as $item) {
                    if (!$item->canSatisfyRequestedQuantityOf(array_get($groupingData, 'initial_qty'))) {
                        throw new RequestedQuantityCannotBeSatisfiedException('Initial quantity of grouping exceeds satisfiable quantity of one or more grouping items.');
                    }
                }

                $grouping = InventoryGrouping::factory([
                    'user_id' => $user->id,
                    'name' => array_get($groupingData, 'name'),
                    'description' => array_get($groupingData, 'description'),
                    'locked' => array_get($groupingData, 'locked'),
                    'price_usd' => array_get($groupingData, 'price_usd'),
                    'initial_qty' => array_get($groupingData, 'initial_qty'),
                ]);

                $grouping->inventoryItems()->sync($inventoryIds);

                $imageData = array_get($groupingData, 'image', []);

                $file = Files::create([
                    'location' => array_get($imageData, 'location'),
                    'key' => array_get($imageData, 'key'),
                    'bucket_name' => array_get($imageData, 'bucket'),
                    'fileable_type' => get_class($grouping),
                    'fileable_id' => $grouping->id
                ]);

                if (array_get($groupingData, 'categories')) {
                    $grouping->tag(array_get($groupingData, 'categories'));
                }

                $grouping->cover_photo_file_id = $file->id;

                $grouping->save();

                event(new InventoryGroupingWasCreatedEvent($grouping));

                $groupings[] = $grouping;
            }
            return $groupings;
        });
    }
}
