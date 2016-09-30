<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use DB;
use Kabooodle\Models\User;
use Kabooodle\Models\Files;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\Categories;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Inventory\AddInventoryCommand;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent;
use Kabooodle\Bus\Commands\Inventory\AddInventoryToSalesCommand;

/**
 * Class AddInventoryCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class AddInventoryCommandHandler
{
    use DispatchesJobs;

    /**
     * @var array
     */
    public $imagesAssociatedToItem = [];

    /**
     * @var array
     */
    public $imagesAsNewItem = [];

    /**
     * @var array
     */
    public $inventoryItems = [];

    /**
     * @param AddInventoryCommand $command
     *
     * @return array
     */
    public function handle(AddInventoryCommand $command)
    {
        // This may be backwards, but we want to check if there are any images first.
        // this is because you can add many images, but each image may be its own inventory item
        // and NOT associated to the actual item we're creating.  Instead, it would just contain
        // the same meta data, except for quantity.  Quantity comes with the data.
        $this->siftThroughImages($command->getImages());

        return DB::transaction(function () use ($command) {
            // First, create the main item.
//            $items[] = $this->buildNewInventoryItem(
//                $command->getActor(),
//                $command->getName(),
//                $command->getDescription(),
//                $command->getPrice(),
//                $command->getQty(),
//                $this->imagesAssociatedToItem
//            );

            // Iterate over the images that are to be duplicates of the original item.
            if ($this->imagesAsNewItem && count($this->imagesAsNewItem) > 0) {
                foreach ($this->imagesAsNewItem as $image) {
                    $items[] = $this->buildNewInventoryItem(
                        $command->getActor(),
                        $command->getName(),
                        $command->getDescription(),
                        $command->getPrice(),
                        $command->getQty(),
                        [$image]
                    );
                }
            }

            // All inventory items we've created get the same (remaining) relationships.
            foreach ($items as $item) {
                $category = Categories::findOrFail($command->getCategoryId());
                $item->categories()->saveMany([$category]);

                $tags = $command->getTags();
                if ($tags) {
                    $item->tag($tags);
                }

                if ($command->getFlashsales() && $item->initial_qty > 0) {
                    $this->dispatchNow(new AddInventoryToSalesCommand(user(), [$item->id], $command->getFlashsales()));
                }

                event(new InventoryItemWasAddedEvent($item));
            }

            return $items;
        });
    }

    /**
     * @param $images
     */
    public function siftThroughImages(array $images)
    {
        if ($images) {
            foreach ($images as $image) {
                $cleanedImage = $this->normalizeImageData($image);
                if (isset($cleanedImage['album_item']) && $cleanedImage['album_item'] == 1) {
                    $this->imagesAssociatedToItem[] = $cleanedImage;
                } else {
                    $this->imagesAsNewItem[] = $cleanedImage;
                }
            }
        }
    }

    /**
     * @param $array
     *
     * @return mixed
     */
    public function normalizeImageData(&$array)
    {
        $array['data'] = json_decode($array['data'], true);

        // Extract keys from data as parent key/values
        foreach ($array['data'] as $k => $v) {
            $array[$k] = $v;
        }
        $array['qty'] = isset($array['qty']) ? $array['qty'] : 1;

        return $array;
    }

    /**
     * @param User       $actor
     * @param            $name
     * @param            $description
     * @param            $price
     * @param            $qty
     * @param array|null $images
     *
     * @return Inventory
     */
    public function buildNewInventoryItem(User $actor, $name, $description, $price, $qty, array $images = null)
    {
        $item = Inventory::factory([
            'name' => $name,
            'description' => $description,
            'initial_qty' => $qty,
            'user_id' => $actor->id,
            'price_usd' => $price,
        ]);

        if ($images && count($images) > 0) {
            $order = 0;
            foreach ($images as $image) {
                $item['initial_qty'] = $image['qty'];
                $item->files()->save(new Files([
                    'location' => $image['location'],
                    'key' => $image['key'],
                    'bucket_name' => $image['bucket'],
                    'fileable_type' => get_class($item),
                    'fileable_id' => $item->id,
                    'sort_order' => $order
                ]));
                $order++;
            }
        }

        $item->save();

        return $item;
    }
}