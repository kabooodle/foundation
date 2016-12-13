<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Inventory;

use Kabooodle\Models\User;
use Illuminate\Bus\Queueable;
use Kabooodle\Models\Contracts\ShoppableInterface;

/**
 * Class TrackInventoryViewCommand
 */
final class TrackInventoryViewCommand
{
    use Queueable;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * TrackInventoryViewCommand constructor.
     * @param User|null $actor
     * @param ShoppableInterface $resource
     * @param string $ipAddress
     */
    public function __construct(User $actor = null, ShoppableInterface $resource, string $ipAddress)
    {
        $this->actor = $actor;
        $this->resource = $resource;
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return User|null
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @return ShoppableInterface
     */
    public function getResource(): ShoppableInterface
    {
        return $this->resource;
    }
}
