<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Flashsale;

use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\FlashSales;

/**
 * Class UpdateFlashsaleCommand
 * @package Kabooodle\Bus\Commands\Flashsale
 */
final class UpdateFlashsaleCommand
{
    /**
     * @var FlashSales
     */
    public $flashsale;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $name;

    /**
     * @var null|string
     */
    public $description;

    /**
     * @var string
     */
    public $privacy;

    /**
     * @var array
     */
    public $adminIds;

    /**
     * @var array
     */
    public $sellerGroups;

    /**
     * @var null
     */
    public $coverPhoto;

    /**
     * @param FlashSales  $flashSale
     * @param User        $user
     * @param string      $name
     * @param string|null $description
     * @param string      $privacy
     * @param array       $adminIds
     * @param array       $sellerGroups
     * @param             $coverPhoto
     */
    public function __construct(
        FlashSales $flashSale,
        User $user,
        string $name,
        string $description = null,
        string $privacy = 'public',
        array $adminIds = [],
        array $sellerGroups,
        $coverPhoto
    ) {
        $this->flashsale = $flashSale;
        $this->user = $user;
        $this->name = $name;
        $this->description = $description;
        $this->privacy = $privacy;
        $this->adminIds = $adminIds;
        $this->sellerGroups = $sellerGroups;
        $this->coverPhoto = $coverPhoto;
    }

    /**
     * @return FlashSales
     */
    public function getFlashsale(): FlashSales
    {
        return $this->flashsale;
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
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPrivacy(): string
    {
        return $this->privacy;
    }

    /**
     * @return array
     */
    public function getAdminIds(): array
    {
        return $this->adminIds;
    }

    /**
     * @return array
     */
    public function getSellerGroups(): array
    {
        return $this->sellerGroups;
    }

    /**
     * @return null
     */
    public function getCoverPhoto()
    {
        return $this->coverPhoto;
    }
}
