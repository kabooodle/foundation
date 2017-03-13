<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Flashsale\Inventory;

use Kabooodle\Models\User;
use Kabooodle\Models\FlashSales;
use Illuminate\Database\Eloquent\Collection;
use Kabooodle\Foundation\Exceptions\GetSellerInventoryException;
use Kabooodle\Bus\Commands\Flashsale\Inventory\GetSellerInventoryCommand;

/**
 * Class GetSellerInventoryCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Flashsale\Inventory
 */
class GetSellerInventoryCommandHandler
{
    /**
     * @param GetSellerInventoryCommand $command
     *
     * @return mixed
     * @throws GetSellerInventoryException
     */
    public function handle(GetSellerInventoryCommand $command)
    {
        /** @var FlashSales $flashSale */
        $flashSale = $command->getFlashsale();

        /** @var Collection $admins */
        $admins = $flashSale->admins;

        /** @var Collection $sellers */
        $sellers = $flashSale->sellers;

        /** @var User $actor */
        $actor = $command->getActor();

        // Need to check if the current flash sale is "active" according to the date range.
        // If it isn't, check the user's credentials (i.e. if they are a seller or admin)
        // If the sale isn't active BUT they have permission to view the items, only display
        // THEIR OWN items.

        // Do we care if the sale has already finished?
        // I think we should so for now we will.
        // Actually, we DO care if the sale has not yet started.
        if ($flashSale->saleIsYetToStart() && $admins->find($actor->id) || $flashSale->saleIsActive()) {
            return $flashSale->enabledInventoryItems;
        } elseif ($flashSale->saleIsYetToStart() && $sellers->find($actor->id)) {
            return $flashSale->enabledInventoryItems->where('seller_id', $actor->id);
        }

        // throw exception that tells us that the sale has ended or something?
        throw new GetSellerInventoryException('The sale has ended');
    }
}
