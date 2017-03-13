<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\User;

/**
 * Class GetClaimCommand
 */
final class GetClaimCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var int
     */
    public $claimId;

    /**
     * GetClaimCommand constructor.
     *
     * @param User $actor
     * @param int  $claimId
     */
    public function __construct(User $actor, int $claimId)
    {
        $this->actor = $actor;
        $this->claimId = $claimId;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return Int
     */
    public function getClaimId(): Int
    {
        return $this->claimId;
    }
}