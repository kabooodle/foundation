<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $qty;

    /**
     * @var int
     */
    public $price;

    /**
     * @var int
     */
    public $categoryId;

    /**
     * @var array
     */
    public $tags;

    /**
     * @var array
     */
    public $flashsales;

    /**
     * @var array
     */
    public $images;

    /**
     * AddInventoryCommand constructor.
     *
     * @param User  $actor
     * @param       $name
     * @param       $description
     * @param       $qty
     * @param       $price
     * @param int $categoryId
     * @param string $tags
     * @param array $flashsales
     * @param array $images
     */
    public function __construct(User $actor, $name, $description, $qty,  $price, $categoryId, $tags = null, array $flashsales = null, array $images = null)
    {
        $this->actor = $actor;
        $this->name = $name;
        $this->description = $description;
        $this->qty = $qty;
        $this->price = $price;
        $this->categoryId = $categoryId;
        $this->tags = $tags;
        $this->flashsales = $flashsales;
        $this->images = $images;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getFlashsales()
    {
        return $this->flashsales;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
}