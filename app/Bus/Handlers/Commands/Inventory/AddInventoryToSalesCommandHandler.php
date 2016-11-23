<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use Kabooodle\Bus\Commands\Social\Facebook\PostPhotoToGroupAlbumCommand;
use Kabooodle\Models\FacebookItems;
use Kabooodle\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedToSaleEvent;
use Kabooodle\Services\Social\Facebook\FacebookSdkFacade;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;

/**
 * Class AddInventoryToSalesCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class AddInventoryToSalesCommandHandler
{
    use DispatchesJobs;

    /**
     * TODO: Abstract this so that its better tested.
     *
     * @param AddInventoryToSalesCommand $command
     *
     * @return mixed|null
     */
    public function handle(AddInventoryToSalesCommand $command)
    {
        $user = $command->getUser();
        $flashSales = $command->getFlashSales();
        $facebookAlbums = $command->getFacebookAlbums();

        // Lazy load the relationship we will be poking at.
        $user->load('flashsaleItems');

        // Determine if we're also adding the item to the user's own store.
        $addedToOwnStore = false;
//        if (in_array($user->username, $flashSaleIds)) {
//            $addedToOwnStore = true;
//            unset($flashSaleIds[array_search($user->username, $flashSaleIds)]);
//        }

        // Make sure we still have an array to associate anything to
//        $this->handleFlashsales($user, $inventoryIds, $flashSaleIds);

        // Blah
        $this->handleFacebookAlbums($user, $facebookAlbums);

        return null;
    }

    /**
     * @param User       $user
     * @param array|null $facebookAlbums
     */
    public function handleFacebookAlbums(User &$user, array $facebookAlbums = [])
    {
        if (count($facebookAlbums) > 0) {

            foreach ($facebookAlbums as $facebookAlbum) {

                // If this album doesn't have an items, then ignore it.
                if(! isset($facebookAlbum['items']) || count($facebookAlbum['items']) == 0) {
                    continue;
                }

                // Set the facebook album id were working with
                $facebookAlbumId = $facebookAlbum['id'];

                // Loop over each of the items
                foreach($facebookAlbum['items'] as $inventoryItem){

                    // Set the inventory item id
                    $inventoryItemId = $inventoryItem['id'];

                    // Make sure we dont add an item to a flashsale that is already there.
                    if (!$this->itemAlreadyInFacebookAlbum($user, $facebookAlbumId, $inventoryItemId)) {

                        // Perform insertion
                        $fb =  new FacebookItems();
                        $fb->seller_id = $user->id;
                        $fb->inventory_id = $inventoryItemId;
                        $fb->facebook_node_id = $facebookAlbumId;
                        $fb->save();

                        $this->dispatch(new PostPhotoToGroupAlbumCommand(
                            $user,
                            $user->getFacebookUserToken(),
                            $fb->id,
                            $facebookAlbumId,
                            $inventoryItem['files'][0]['location']
                        ));

                        // Fire event
                        event(new InventoryItemWasAddedToSaleEvent($user, $facebookAlbumId, $inventoryItemId));
                    }
                }
            }

            $user->load('facebookItems');
        }
    }

    /**
     * @param User $user
     * @param      $inventoryIds
     * @param      $flashSaleIds
     *
     * @return void
     */
    public function handleFlashsales(User &$user, $inventoryIds, $flashSaleIds)
    {
        if (count($flashSaleIds) > 0) {
            $flashSaleItems = $user->flashsaleItems();
            foreach ($inventoryIds as $inventoryId) {
                foreach ($flashSaleIds as $flashSaleId) {
                    // Make sure we dont add an item to a flashsale that is already there.
                    if (!$this->itemAlreadyInFlashSale($user, $flashSaleId, $inventoryId)) {

                        // Perform insertion
                        $flashSaleItems->attach($flashSaleId, ['inventory_id' => $inventoryId]);

                        // Fire event
                        event(new InventoryItemWasAddedToSaleEvent($user, $flashSaleId, $inventoryId));
                    }
                }
            }

            $user->load('flashsaleItems');
        }
    }

    /**
     * @param User $user
     * @param      $flashsaleId
     * @param      $inventoryId
     *
     * @return mixed
     */
    protected function itemAlreadyInFlashSale(User $user, $flashsaleId, $inventoryId)
    {
        $user->load('flashsaleItems');

        return $user->flashsaleItems->filter(function ($item) use ($flashsaleId, $inventoryId) {
            return $item->id == $flashsaleId && $item->pivot->inventory_id == $inventoryId;
        })->first();
    }

    /**
     * @param User $user
     * @param int  $facebookAlbumId
     * @param int  $inventoryId
     *
     * @return mixed
     */
    protected function itemAlreadyInFacebookAlbum(User $user, int $facebookAlbumId, int $inventoryId)
    {
        $user->load('facebookItems');

        return $user->facebookItems->filter(function ($item) use ($facebookAlbumId, $inventoryId) {
            return $item->facebook_node_id == $facebookAlbumId && $item->inventory_id == $inventoryId;
        })->first();
    }
}
