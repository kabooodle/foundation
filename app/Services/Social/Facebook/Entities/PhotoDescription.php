<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Services\Social\Facebook\Entities;

use Kabooodle\Models\ListingItems;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class PhotoDescription
 * @package Kabooodle\Services\Social\Facebook\Entities
 */
class PhotoDescription
{
    use ObfuscatesIdTrait;

    /**
     * @var ListingItems
     */
    protected $listingItem;

    /**
     * @param ListingItems $listingItems
     */
    public function __construct(ListingItems $listingItems)
    {
        $this->listingItem = $listingItems;
    }

    /**
     * @return string
     */
    public function getClaimUrl()
    {
        $id = $this->obfuscateIdToString($this->listingItem->id);

        return str_replace('api', 'app', route('externalclaim.show', [$id]));
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return " Claim here: ".ltrim($this->getClaimUrl(), 'http://');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getComment();
    }
}
