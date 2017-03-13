<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Flashsale;

use Kabooodle\Models\Dates\StartsAndEndsAt;
use Kabooodle\Models\User;

/**
 * Class AddFlashsaleCommand
 * @package Kabooodle\Bus\Commands\Flashsale
 */
class AddFlashsaleCommand
{
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
     * @var StartsAndEndsAt
     */
    public $startsAndEndsAt;

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
     * @param User            $user
     * @param string          $name
     * @param string|null     $description
     * @param StartsAndEndsAt $startsAndEndsAt
     * @param string          $privacy
     * @param array           $adminIds
     * @param array           $sellerGroups
     * @param null            $coverPhoto
     */
    public function __construct(
        User $user,
        string $name,
        string $description = null,
        StartsAndEndsAt $startsAndEndsAt,
        string $privacy = 'public',
        array $adminIds = [],
        array $sellerGroups,
        $coverPhoto = null
    ) {
        $this->user = $user;
        $this->name = $name;
        $this->description = $description;
        $this->startsAndEndsAt = $startsAndEndsAt;
        $this->privacy = $privacy;
        $this->adminIds = $adminIds;
        $this->sellerGroups = $sellerGroups;
        $this->coverPhoto = $coverPhoto;
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
     * @return StartsAndEndsAt
     */
    public function getStartsAndEndsAt(): StartsAndEndsAt
    {
        return $this->startsAndEndsAt;
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
