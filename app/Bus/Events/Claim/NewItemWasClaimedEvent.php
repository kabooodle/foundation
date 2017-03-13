<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Claim;

use Kabooodle\Models\Claims;

/**
 * Class NewItemWasClaimedEvent
 * @package Kabooodle\Bus\Events\Claim
 */
final class NewItemWasClaimedEvent
{
    /**
     * @var Claims
     */
    private $claim;

    /**
     * NewItemWasClaimedEvent constructor.
     *
     * @param Claims $claim
     */
    public function __construct(Claims $claim)
    {
        $this->claim = $claim;
    }

    /**
     * @return Claims
     */
    public function getclaim()
    {
        return $this->claim;
    }
}
