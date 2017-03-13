<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use DB;
use Kabooodle\Bus\Commands\Inventory\GetUsedInventoryCategoriesCommand;

/**
 * Class GetUsedInventoryCategoriesCommandHandler
 */
class GetUsedInventoryCategoriesCommandHandler
{
    /**
     * @param GetUsedInventoryCategoriesCommand $command
     * @return \Illuminate\Support\Collection|null
     */
    public function handle(GetUsedInventoryCategoriesCommand $command)
    {
        $user = $command->getActor();
        $data = DB::table('tagging_tagged')
            ->join('listables', 'taggable_id', '=', 'listables.id')
            ->where('listables.user_id', '=', $user->id)
            ->groupBy('tagging_tagged.id')
            ->get();

        return count($data) > 0 ? collect($data) : null;
    }
}
