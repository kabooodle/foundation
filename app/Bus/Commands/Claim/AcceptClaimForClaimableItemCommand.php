<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Carbon\Carbon;
use Kabooodle\Models\User;

/**
 * Class AcceptClaimForClaimableItemCommand.
 */
class AcceptClaimForClaimableItemCommand
{
    /**
     * AcceptClaimForClaimableItemCommand constructor.
     *
     * @param User        $user
     * @param             $claimId
     * @param null        $acceptedPrice
     * @param Carbon|null $timestamp
     * @param null        $notes
     */
    public function __construct(User $user, $claimId, $acceptedPrice = null, Carbon $timestamp = null, $notes = null)
    {
        $this->user = $user;
        $this->claimId = $claimId;
        $this->acceptedPrice = $acceptedPrice;
        $this->timestamp = $timestamp;
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

    public function getNotes()
    {
        return $this->notes;
    }

    public function getTimestamp()
    {
        return $this->timestamp ?: Carbon::now();
    }

    public function getAcceptedPrice()
    {
        return $this->acceptedPrice ?: null;
    }
}
