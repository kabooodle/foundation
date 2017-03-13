<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Credits;

use Kabooodle\Models\User;

/**
 * Class PurchaseCreditsForUserCommand
 * @package Kabooodle\Bus\Commands\Profile
 */
final class PurchaseCreditsForUserCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var int
     */
    public $creditTypeId;

    /**
     * PurchaseCreditsForUserCommand constructor.
     *
     * @param User $actor
     * @param int  $creditTypeId
     */
    public function __construct(User $actor, $creditTypeId)
    {
        $this->actor = $actor;
        $this->creditTypeId = $creditTypeId;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return int
     */
    public function getCreditTypeId()
    {
        return $this->creditTypeId;
    }
}
