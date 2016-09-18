<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Inventory;

use Kabooodle\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class AddInventoryToSalesCommand
 * @package Kabooodle\Bus\Commands\Inventory
 */
class AddInventoryToSalesCommand implements ShouldQueue
{
    use SerializesModels;
    /**
     * @var User
     */
    protected $user;

    /**
     * @var array
     */
    protected $inventoryIds;

    /**
     * @var array
     */
    protected $flashSalesIds;

    /**
     * @var array
     */
    protected $facebookAlbumIds;

    /**
     * AddInventoryToSalesCommand constructor.
     *
     * @param User  $user
     * @param array $inventoryIds
     * @param array $flashSalesIds
     * @param array $facebookAlbumIds
     */
    public function __construct(User $user, array $inventoryIds, array $flashSalesIds = [], array $facebookAlbumIds = [])
    {
        $this->user = $user;
        $this->inventoryIds = $inventoryIds;
        $this->flashSalesIds = $flashSalesIds;
        $this->facebookAlbumIds = $facebookAlbumIds;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getInventoryIds()
    {
        return $this->inventoryIds;
    }

    /**
     * @return array
     */
    public function getFlashSalesIds()
    {
        return $this->flashSalesIds;
    }

    /**
     * @return array
     */
    public function getFacebookAlbumIds()
    {
        return $this->facebookAlbumIds;
    }
}