<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\User;

/**
 * Class RejectClaimForInventoryItemCommand.
 */
class RejectClaimForInventoryItemCommand
{
    /**
     * RejectClaimForInventoryItemCommand constructor.
     *
     * @param User $user
     * @param      $claimId
     * @param null $notes
     */
    public function __construct(User $user, $claimId, $notes = null)
    {
        $this->user = $user;
        $this->claimId = $claimId;
        $this->notes = $notes;
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

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
