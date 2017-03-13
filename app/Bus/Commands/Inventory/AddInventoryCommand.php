<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Inventory;

use Kabooodle\Models\User;

/**
 * Class AddInventoryCommand
 * @package Kabooodle\Bus\Commands\Inventory
 */
final class AddInventoryCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var int
     */
    public $typeId;

    /**
     * @var int
     */
    public $styleId;

    /**
     * @var string
     */
    public $price;

    /**
     * @var string
     */
    public $wholesalePrice;

    /**
     * @var array
     */
    public $sizings;

    /**
     * @var null|string
     */
    public $description;

    /**
     * AddInventoryCommand constructor.
     *
     * @param User        $actor
     * @param int         $typeId
     * @param int         $styleId
     * @param string      $price
     * @param string      $wholesalePrice
     * @param array       $sizings
     * @param string|null $description
     */
    public function __construct(User $actor, int $typeId, int $styleId, string $price, string $wholesalePrice, array $sizings, string $description = null)
    {
        $this->actor = $actor;
        $this->typeId = $typeId;
        $this->styleId = $styleId;
        $this->price = $price;
        $this->wholesalePrice = $wholesalePrice;
        $this->sizings = $sizings;
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
     * @return int
     */
    public function getTypeId(): int
    {
        return $this->typeId;
    }

    /**
     * @return int
     */
    public function getStyleId(): int
    {
        return $this->styleId;
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
    public function getWholesalePrice(): string
    {
        return $this->wholesalePrice;
    }

    /**
     * @return array
     */
    public function getSizings(): array
    {
        return $this->sizings;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }
}