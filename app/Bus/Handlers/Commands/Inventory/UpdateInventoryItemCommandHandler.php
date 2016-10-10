<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use DB;
use InvalidArgumentException;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\InventoryTypeStyles;
use Kabooodle\Bus\Commands\Inventory\UpdateInventoryItemCommand;

/**
 * Class UpdateInventoryItemCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class UpdateInventoryItemCommandHandler
{
    /**
     * @param UpdateInventoryItemCommand $command
     *
     * @return mixed
     */
    public function handle(UpdateInventoryItemCommand $command)
    {
        $style = InventoryTypeStyles::findOrFail($command->getStyleId());

        // Check that the requested size belongs to the requested style
        // Could move this to the model observer.
        if (! $style->sizes->find($command->getSizeId())) {
            throw new InvalidArgumentException;
        }

        return DB::transaction(function() use ($command) {
            /** @var Inventory $item */
            $item = $command->getItem();
            $item->inventory_type_styles_id = $command->getStyleId();
            $item->inventory_sizes_id = $command->getSizeId();
            $item->description = $command->getDescription();
            $item->initial_qty = $command->getQty();
            $item->price_usd = $command->getPrice();
            if (!empty($command->getCategories())) {
                $item->retag($command->getCategories());
            } else {
                $item->untag();
            }

            $item->save();

            return $item;
        });
    }
}