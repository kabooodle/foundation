<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\InventoryGroupings;

use Kabooodle\Models\User;

/**
 * Class CreateInventoryGroupingsCommand
 * @package Kabooodle\Bus\Commands\InventoryGroupings
 */
final class CreateInventoryGroupingsCommand
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
    protected $image;

    /**
     * @var array
     */
    protected $inventory;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * @var string
     */
    protected $categories;

    /**
     * @var bool
     */
    protected $autoAdd;

    /**
     * @var bool
     */
    protected $maxQuantity;

    /**
     * CreateInventoryGroupingsCommand constructor.
     *
     * @param User $user
     * @param string $name
     * @param bool $locked
     * @param float $price
     * @param int $initialQty
     * @param array $image
     * @param array $inventory
     * @param null $description
     * @param string $categories
     * @param bool $autoAdd
     * @param bool $maxQuantity
     */
    public function __construct(
        User $user,
        string $name,
        bool $locked,
        float $price,
        int $initialQty,
        array $image,
        array $inventory,
        $description = null,
        string $categories,
        bool $autoAdd,
        bool $maxQuantity)
    {
        $this->user = $user;
        $this->name = $name;
        $this->locked = $locked;
        $this->price = $price;
        $this->initialQty = $initialQty;
        $this->image = $image;
        $this->inventory = $inventory;
        $this->description = $description;
        $this->categories = $categories;
        $this->autoAdd = $autoAdd;
        $this->maxQuantity = $maxQuantity;
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
    public function getImage(): array
    {
        return $this->image;
    }

    /**
     * @return array
     */
    public function getInventory(): array
    {
        return $this->inventory;
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

    /**
     * @return boolean
     */
    public function isAutoAdd(): bool
    {
        return $this->autoAdd;
    }

    /**
     * @return boolean
     */
    public function isMaxQuantity(): bool
    {
        return $this->maxQuantity;
    }
}