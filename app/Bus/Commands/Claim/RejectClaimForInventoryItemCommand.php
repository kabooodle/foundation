<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\User;

/**
 * Class RejectClaimForInventoryItemCommand
 * @package Kabooodle\Bus\Commands\Claim
 */
class RejectClaimForInventoryItemCommand
{
    /**
     * RejectClaimForInventoryItemCommand constructor.
     *
     * @param User $user
     * @param      $claimId
     */
    public function __construct(User $user, $claimId, $reason = null)
    {
        $this->user = $user;
        $this->claimId = $claimId;
    }

    /**
     * @return mixed
     */
    public function getClaimId()
    {
        return $this->claimId;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}