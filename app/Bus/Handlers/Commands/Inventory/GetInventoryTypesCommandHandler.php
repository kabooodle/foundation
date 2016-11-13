<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Kabooodle\Models\InventoryType;
use Kabooodle\Bus\Commands\Inventory\GetInventoryTypesCommand;

/**
 * Class GetInventoryTypesCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class GetInventoryTypesCommandHandler
{
    /**
     * @param GetInventoryTypesCommand $command
     *
     * @return mixed
     */
    public function handle(GetInventoryTypesCommand $command)
    {
        $types = InventoryType::withStylesAndSizes()->get();

        if ($command->getSlugs() && count($command->getSlugs()) > 0) {
            return $types->filter(function ($type) use ($command) {
                return in_array($type->slug, $command->getSlugs());
            });
        }

        return $types;
    }
}
