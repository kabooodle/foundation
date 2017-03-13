<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Claim;

use Kabooodle\Models\User;
use Kabooodle\Models\Claims;
use Illuminate\Queue\SerializesModels;

/**
 * Class ClaimWasRejectedEvent
 * @package Kabooodle\Bus\Events
 */
final class ClaimWasRejectedEvent
{
    use SerializesModels;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var Claims
     */
    public $claim;

    /**
     * ClaimWasRejectedEvent constructor.
     *
     * @param User   $actor
     * @param Claims $claim
     */
    public function __construct(User $actor, Claims $claim)
    {
        $this->actor = $actor;
        $this->claim = $claim;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return Claims
     */
    public function getClaim()
    {
        return $this->claim;
    }
}
