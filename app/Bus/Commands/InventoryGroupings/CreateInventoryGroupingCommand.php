<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\InventoryGroupings;

use Kabooodle\Models\User;

/**
 * Class CreateInventoryGroupingCommand
 * @package Kabooodle\Bus\Commands\InventoryGroupings
 */
final class CreateInventoryGroupingCommand
{
    /**
     * @var User
     */
    protected $user;

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
     * CreateInventoryGroupingCommand constructor.
     *
     * @param User $user
     * @param string $name
     * @param bool $locked
     * @param string $price
     * @param $initialQty
     * @param array $images
     * @param array $inventoryIds
     * @param null $description
     */
    public function __construct(
        User $user,
        string $name,
        bool $locked,
        string $price,
        $initialQty,
        array $images,
        array $inventoryIds,
        $description = null)
    {
        $this->user = $user;
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
    public function getUser(): User
    {
        return $this->user;
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