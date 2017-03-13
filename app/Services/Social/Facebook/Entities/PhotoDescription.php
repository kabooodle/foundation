<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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

        return str_replace(['https://', 'http://'], '', str_replace(['api.', 'app.', 'api', 'app'], '', route('externalclaim.show', [$id])));
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return " Claim here: ". $this->getClaimUrl();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getComment();
    }
}
