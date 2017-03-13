<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Inventory;

use Kabooodle\Models\Inventory;
use Kabooodle\Models\User;

/**
 * Class UpdateInventoryItemCommand
 * @package Kabooodle\Bus\Commands\Inventory
 */
final class UpdateInventoryItemCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var Inventory
     */
    public $item;

    /**
     * @var int
     */
    public $styleId;

    /**
     * @var int
     */
    public $sizeId;

    /**
     * @var float
     */
    public $price;

    /**
     * @var float
     */
    public $wholesalePrice;

    /**
     * @var int
     */
    public $qty;

    /**
     * @var array
     */
    public $images;

    /**
     * @var string
     */
    public $coverPhotoKey;

    /**
     * @var null|string
     */
    public $description;

    /**
     * @var null|string
     */
    public $categories;

    /**
     * @var string
     */
    public $uuid;

    /**
     * @param User        $actor
     * @param Inventory   $item
     * @param int         $styleId
     * @param int         $sizeId
     * @param float       $price
     * @param float       $wholesalePrice
     * @param int         $qty
     * @param array       $images
     * @param string      $coverPhotoKey
     * @param string|null $description
     * @param string|null $categories
     * @param string      $uuid
     */
    public function __construct(
        User $actor,
        Inventory $item,
        int $styleId,
        int $sizeId,
        float $price,
        float $wholesalePrice,
        int $qty,
        array $images,
        string $coverPhotoKey,
        string $description = null,
        string $categories = null,
        string $uuid
    ) {
        $this->actor = $actor;
        $this->item = $item;
        $this->styleId = $styleId;
        $this->sizeId = $sizeId;
        $this->price = $price;
        $this->wholesalePrice = $wholesalePrice;
        $this->qty = $qty;
        $this->images = $images;
        $this->coverPhotoKey = $coverPhotoKey;
        $this->description = $description;
        $this->categories = $categories;
        $this->uuid = $uuid;
    }

    /**
     * @return null|string
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
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
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * @return int
     */
    public function getSizeId(): int
    {
        return $this->sizeId;
    }

    /**
     * @return int
     */
    public function getStyleId(): int
    {
        return $this->styleId;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return string
     */
    public function getCoverPhotoKey(): string
    {
        return $this->coverPhotoKey;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return Inventory
     */
    public function getItem(): Inventory
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return float
     */
    public function getWholesalePrice(): float
    {
        return $this->wholesalePrice;
    }
}