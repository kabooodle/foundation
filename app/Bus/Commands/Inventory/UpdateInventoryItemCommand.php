<?php

namespace Kabooodle\Bus\Commands\Inventory;

use Kabooodle\Models\Inventory;

/**
 * Class UpdateInventoryItemCommand
 * @package Kabooodle\Bus\Commands\Inventory
 */
class UpdateInventoryItemCommand
{
    /**
     * @var Inventory
     */
    private $item;

    /**
     * UpdateInventoryItemCommand constructor.
     *
     * @param Inventory $item
     * @param array     $attributes
     */
    public function __construct(Inventory $item, $attributes = [])
    {
        $this->item = $item;
        $this->attributes = $attributes;
    }

    /**
     * @return Inventory
     */
    public function getItem()
    {
        return $this->item;
    }
}