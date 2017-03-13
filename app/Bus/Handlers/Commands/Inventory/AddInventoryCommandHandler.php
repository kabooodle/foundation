<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Inventory;

use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Inventory\AddInventoryCommand;
use Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent;
use Kabooodle\Models\Files;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\InventoryType;
use Kabooodle\Models\User;

/**
 * Class AddInventoryCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Inventory
 */
class AddInventoryCommandHandler
{
    use DispatchesJobs;

    /**
     * @param AddInventoryCommand $command
     *
     * @return array
     */
    public function handle(AddInventoryCommand $command)
    {
        $sizings = $command->getSizings();

        $inventoryType = InventoryType::findOrFail($command->getTypeId());

        // Confirm the style is associated to the type;
        $inventoryStyle = $inventoryType->styles->find($command->getStyleId());

        if (!$inventoryStyle) {
            throw new ModelNotFoundException("Style model [{$command->getStyleId()}] not found for Type [{$command->getTypeId()}]");
        }

        return DB::transaction(function () use ($command, $sizings, $inventoryType, $inventoryStyle) {
            // Array of all inventory items.
            $items = [];
            $data = [
                'actor' => $command->getActor(),
                'type_id' => $inventoryType->id,
                'style_id' => $inventoryStyle->id,
                'price_usd' => $command->getPrice(),
                'description' => $command->getDescription(),
                'wholesale_price_usd' => $command->getWholesalePrice()
            ];

            // Loop over sizings for size_id and categories
            foreach ($sizings as $sizing) {

                // confirm the size is associated to the style.
                $size = $inventoryStyle->sizes->find($sizing['size_id']);
                if (!$size) {
                    throw new ModelNotFoundException("Size model [{$sizing['size_id']}] not found for Style [{$command->getStyleId()}]");
                }

                // Get the size categories.
                $categories = isset($sizing['categories']) ? implode(',',$sizing['categories']) : null;

                // Normalize the images array.


                // loop over the images inside sizings for: quantity, and image data.
                foreach ($sizing['images'] as $sizeImage) {
                    $this->normalizeImageData($sizeImage);
                    $item = $this->buildNewInventoryItem(
                        $data['actor'],
                        $data['type_id'],
                        $data['style_id'],
                        $sizing['size_id'],
                        $data['description'],
                        $data['price_usd'],
                        $data['wholesale_price_usd'],
                        $categories,
                        $sizeImage
                    );

                    // Add the item to the array of items.
                    $items[] = $item;
                }
            }

            // TODO: Make this a plural event name/handler
            event(new InventoryItemWasAddedEvent($items));

            return $items;
        });
    }

    /**
     * @param User        $actor
     * @param int         $typeId
     * @param int         $styleId
     * @param int         $sizeId
     * @param string|null $description
     * @param             $price
     * @param             $wholesalePrice
     * @param string      $categories
     * @param array       $image
     *
     * @return Inventory
     */
    public function buildNewInventoryItem(
        User $actor,
        int $typeId,
        int $styleId,
        int $sizeId,
        string $description = null,
        $price,
        $wholesalePrice,
        string $categories = null,
        array $image
    ) {

        // Build our item.
        $item = Inventory::factory([
            'user_id' => $actor->id,
            'inventory_type_id' => $typeId,
            'inventory_type_styles_id' => $styleId,
            'inventory_sizes_id' => $sizeId,
            'description' => $description,
            'price_usd' => $price,
            'wholesale_price_usd' => $wholesalePrice,
            'initial_qty' => $image['qty']
        ]);


        $file = Files::create([
            'location' => $image['location'],
            'key' => $image['key'],
            'bucket_name' => $image['bucket'],
            'fileable_type' => get_class($item),
            'fileable_id' => $item->id
        ]);
        // Associate files(images) to the item.
        $item->files()->save($file);

        // Associate categories to the item.
        // They are passed as a comma separated string.
        if ($categories) {
            $item->tag($categories);
        }

        // When CREATING a new inventory item, we only allow 1 photo to be added.
        // This means, each photo is also the cover photo.  :)
        $item->cover_photo_file_id = $file->id;

        $item->save();

        return $item;
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
}
