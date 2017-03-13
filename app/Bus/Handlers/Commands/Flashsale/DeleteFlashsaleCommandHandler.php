<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale;

use DB;
use Kabooodle\Models\FlashSales;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Flashsale\DeleteFlashsaleCommand;

/**
 * Class DeleteFlashsaleCommandHandler
 */
class DeleteFlashsaleCommandHandler extends AbstractFlashsaleCommandHandler
{
    use DispatchesJobs;

    /**
     * @param DeleteFlashsaleCommand $command
     *
     * @return mixed
     */
    public function handle(DeleteFlashsaleCommand $command)
    {
        return DB::transaction(function() use ($command) {

            /** @var FlashSales $flashsale */
            $flashsale = $command->getFlashSale();

            $flashsale->coverimage()->delete();
            $flashsale->admins()->delete();
            $flashsale->sellerGroups()->delete();

            $flashsale->delete();

            return true;
        });
    }
}
