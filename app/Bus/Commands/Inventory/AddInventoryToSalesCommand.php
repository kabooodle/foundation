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
final class AddInventoryToSalesCommand implements ShouldQueue
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var array
     */
    public $flashSales;

    /**
     * @var array
     */
    public $facebookAlbums;

    /**
     * @param User  $user
     * @param array $flashSales
     * @param array $facebookAlbums
     */
    public function __construct(User $user, array $flashSales = [], array $facebookAlbums = [])
    {
        $this->user = $user;
        $this->flashSales = $flashSales;
        $this->facebookAlbums = $facebookAlbums;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getFlashSales(): array
    {
        return $this->flashSales;
    }

    /**
     * @return array
     */
    public function getFacebookAlbums(): array
    {
        return $this->facebookAlbums;
    }
}
