<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
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
    protected $user;

    /**
     * @var InventoryGrouping
     */
    protected $grouping;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var int
     */
    protected $initialQty;

    /**
     * @var array
     */
    protected $images;

    /**
     * @var array
     */
    protected $coverPhoto;

    /**
     * @var array
     */
    protected $inventoryIds;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * @var string
     */
    protected $categories;

    /**
     * UpdateInventoryGroupingCommand constructor.
     *
     * @param User $user
     * @param InventoryGrouping $grouping
     * @param string $name
     * @param bool $locked
     * @param float $price
     * @param int $initialQty
     * @param array $images
     * @param array $coverPhoto
     * @param array $inventoryIds
     * @param null $description
     * @param string $categories
     */
    public function __construct(
        User $user,
        InventoryGrouping $grouping,
        string $name,
        bool $locked,
        float $price,
        int $initialQty,
        array $images,
        array $coverPhoto,
        array $inventoryIds,
        $description = null,
        string $categories)
    {
        $this->user = $user;
        $this->grouping = $grouping;
        $this->name = $name;
        $this->locked = $locked;
        $this->price = $price;
        $this->initialQty = $initialQty;
        $this->images = $images;
        $this->coverPhoto = $coverPhoto;
        $this->inventoryIds = $inventoryIds;
        $this->description = $description;
        $this->categories = $categories;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return InventoryGrouping
     */
    public function getGrouping(): InventoryGrouping
    {
        return $this->grouping;
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
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getInitialQty(): int
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
    public function getCoverPhoto(): array
    {
        return $this->coverPhoto;
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

    /**
     * @return string
     */
    public function getCategories(): string
    {
        return $this->categories;
    }
}