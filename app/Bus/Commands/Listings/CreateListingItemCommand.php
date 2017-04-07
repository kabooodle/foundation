<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\Contracts\ListableInterface;
use Kabooodle\Models\Email;
use Kabooodle\Models\Listable;
use Kabooodle\Models\User;

/**
 * Class CreateListingItemCommand.
 */
class CreateListingItemCommand
{
    protected $owner;
    protected $item;

    /**
     * ClaimListedItemCommand constructor.
     * @param User $claimedBy
     * @param Listable $item
     */
    public function __construct(
        User $owner,
        Listable $item)
    {
        $this->owner = $owner;
        $this->item = $item;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return Listable
     */
    public function getItem()
    {
        return $this->item;
    }
}