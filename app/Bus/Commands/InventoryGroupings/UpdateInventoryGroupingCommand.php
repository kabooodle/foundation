<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\InventoryGroupings;

use Kabooodle\Models\InventoryGrouping;

/**
 * Class UpdateInventoryGroupingCommand
 * @package Kabooodle\Bus\Commands\InventoryGroupings
 */
final class UpdateInventoryGroupingCommand
{
    /**
     * @var InventoryGrouping
     */
    public $inventoryGrouping;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $price;

    /**
     * @var array
     */
    public $images;

    /**
     * @var array
     */
    public $inventoryIds;

    /**
     * @var null|string
     */
    public $description;

    /**
     * UpdateInventoryGroupingCommand constructor.
     *
     * @param InventoryGrouping $inventoryGrouping
     * @param string $name
     * @param string $price
     * @param array $images
     * @param array $inventoryIds
     * @param string|null $description
     */
    public function __construct(
        InventoryGrouping $inventoryGrouping,
        string $name,
        string $price,
        array $images,
        array $inventoryIds,
        string $description = null)
    {
        $this->inventoryGrouping = $inventoryGrouping;
        $this->name = $name;
        $this->price = $price;
        $this->images = $images;
        $this->inventoryIds = $inventoryIds;
        $this->description = $description;
    }

    /**
     * @return InventoryGrouping
     */
    public function getInventoryGrouping(): InventoryGrouping
    {
        return $this->inventoryGrouping;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return array
     */
    public function getInventoryIds(): array
    {
        return $this->inventoryIds;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }
}