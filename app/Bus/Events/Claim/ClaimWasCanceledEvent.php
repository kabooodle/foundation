<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Claim;

use Kabooodle\Models\Claims;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClaimWasCanceledEvent
 * @package Kabooodle\Bus\Events
 */
final class ClaimWasCanceledEvent
{
    use SerializesModels;

    /**
     * @var Claims
     */
    public $claim;

    /**
     * ClaimWasCanceledEvent constructor.
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
    public function getClaim()
    {
        return $this->claim;
    }
}
