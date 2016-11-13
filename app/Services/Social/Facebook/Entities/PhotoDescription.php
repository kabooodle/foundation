<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Services\Social\Facebook\Entities;

use Kabooodle\Models\FacebookItems;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class PhotoDescription
 * @package Kabooodle\Services\Social\Facebook\Entities
 */
class PhotoDescription
{
    use ObfuscatesIdTrait;

    /**
     * @var FacebookItems
     */
    protected $facebookItem;

    /**
     * @var string
     */
    protected $description;

    /**
     * PhotoDescription constructor.
     *
     * @param FacebookItems $facebookItem
     * @param string        $description
     */
    public function __construct(FacebookItems $facebookItem, $description = '')
    {
        $this->facebookItem = $facebookItem;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getClaimUrl()
    {
        $id = $this->obfuscateIdToString($this->facebookItem->id);

        return route('externalclaim.show', [$id]);
    }

    /**
     * @return string
     */
    public function getComment()
    {
        $preMessage = $this->description ? $this->description."\n" : null;

        return $preMessage." Claim here: ".ltrim($this->getClaimUrl(), 'http://');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getComment();
    }
}
