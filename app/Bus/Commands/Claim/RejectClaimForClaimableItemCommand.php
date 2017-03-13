<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\User;

/**
 * Class RejectClaimForInventoryItemCommand.
 */
final class RejectClaimForClaimableItemCommand
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $claimUuid;

    /**
     * @var null
     */
    public $notes;

    /**
     * RejectClaimForInventoryItemCommand constructor.
     *
     * @param User $user
     * @param string $claimUuid
     * @param null $notes
     */
    public function __construct(User $user, $claimUuid, $notes = null)
    {
        $this->user = $user;
        $this->claimUuid = $claimUuid;
        $this->notes = $notes;
    }

    /**
     * @return mixed
     */
    public function getClaimUuid(): string
    {
        return $this->claimUuid;
    }

    /**
     * @return User
     */
    public function getUser(): User
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
