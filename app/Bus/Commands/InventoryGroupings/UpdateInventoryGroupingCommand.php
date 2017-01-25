<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\InventoryGroupings;

use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Models\User;

/**
 * Class UpdateInventoryGroupingCommand
 * @package Kabooodle\Bus\Commands\InventoryGroupings
 */
final class UpdateInventoryGroupingCommand
{
    /**
     * @var User
     */
    protected $actor;

    /**
     * @var InventoryGrouping
     */
    protected $inventoryGrouping;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var string
     */
    protected $price;

    /**
     * @var string
     */
    protected $initialQty;

    /**
     * @var array
     */
    protected $images;

    /**
     * @var array
     */
    protected $inventoryIds;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * UpdateInventoryGroupingCommand constructor.
     *
     * @param User $actor
     * @param InventoryGrouping $inventoryGrouping
     * @param string $name
     * @param bool $locked
     * @param string $price
     * @param $initialQty
     * @param array $images
     * @param array $inventoryIds
     * @param null $description
     */
    public function __construct(
        User $actor,
        InventoryGrouping $inventoryGrouping,
        string $name,
        bool $locked,
        string $price,
        $initialQty,
        array $images,
        array $inventoryIds,
        $description = null)
    {
        $this->actor = $actor;
        $this->inventoryGrouping = $inventoryGrouping;
        $this->name = $name;
        $this->locked = $locked;
        $this->price = $price;
        $this->initialQty = $initialQty;
        $this->images = $images;
        $this->inventoryIds = $inventoryIds;
        $this->description = $description;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
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
     * @return boolean
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getInitialQty(): string
    {
        return $this->initialQty;
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