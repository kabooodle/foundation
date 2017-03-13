<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\User;
use InvalidArgumentException;

/**
 * Class ToggleShippingMethodOnClaimCommand
 */
final class ToggleShippingMethodOnClaimCommand
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
     * @var string
     */
    public $newShippingMethod;

    /**
     * ToggleShippingMethodOnClaim constructor.
     *
     * @param User   $actor
     * @param int    $claimId
     * @param string $newShippingMethod
     */
    public function __construct(User $actor, int $claimId, $newShippingMethod = 'external')
    {
        if(!in_array($newShippingMethod, ['external', 'kabooodle'])) {
            throw new InvalidArgumentException;
        }

        $this->actor = $actor;
        $this->claimId = $claimId;
        $this->newShippingMethod = $newShippingMethod;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return int
     */
    public function getClaimId(): int
    {
        return $this->claimId;
    }

    /**
     * @return string
     */
    public function getNewShippingMethod(): string
    {
        return $this->newShippingMethod;
    }
}