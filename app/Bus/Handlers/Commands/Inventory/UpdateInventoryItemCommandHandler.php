<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Kabooodle\Models\Categories;
use Kabooodle\Models\Inventory;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;

/**
 * Class UpdateInventoryItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class UpdateInventoryItemCommandHandler
{
    public function handle(UpdateInventoryItemCommand $command)
    {
        /** @var Inventory $item */
        $item = $command->getItem();
        $item->name = array_get($command->attributes, 'name', $item->name);
        $item->description = array_get($command->attributes, 'description', $item->description);
        $item->initial_qty = array_get($command->attributes, 'initial_qty', $item->initial_qty);
        $item->price_usd = array_get($command->attributes, 'price_usd', 0);
        if (!empty($command->attributes['tags'])) {
            $item->retag($command->attributes['tags']);
        } else {
            $item->untag();
        }

        $requestCategories = array_get($command->attributes, 'categories');
        if ($requestCategories) {
            $categories = Categories::whereIn('id', [$requestCategories])->get();
        } else {
            $categories = [];
        }

        $item->categories()->sync($categories);

        $item->save();
    }
}